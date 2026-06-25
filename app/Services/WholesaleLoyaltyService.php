<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\WholesaleCreditLog;
use App\Models\WholesaleRedemption;
use App\Models\WholesaleCustomerRedemption;

class WholesaleLoyaltyService
{
    // 1 credit per 3.333 IDR → 300.000 credits at 1M
    const CREDIT_PER_RUPIAH = 3333;

    // Rank thresholds — kelipatan 2x, tanpa batas atas
    const RANK_NAMES = [
        'Regular', 'Bronze', 'Silver', 'Gold', 'Platinum',
        'Diamond', 'Exclusive', 'Premium', 'Elite', 'Royal',
        'Imperial', 'Legend', 'Mythic', 'Transcend', 'Infinite',
    ];

    /**
     * Dapatkan rank berdasarkan total belanja seumur hidup
     * — tanpa batas, terus naik hingga triliunan
     */
    public static function getRank(float $lifetimeSpend): array
    {
        $threshold = 0;
        foreach (self::RANK_NAMES as $i => $name) {
            $threshold = $i === 0 ? 0 : 5000000 * pow(2, $i - 1);
            if ($i === 0) continue; // Regular always at 0
            if ($lifetimeSpend < $threshold) {
                $prevThreshold = $i === 1 ? 0 : 5000000 * pow(2, $i - 2);
                return [
                    'name' => self::RANK_NAMES[$i - 1],
                    'label' => self::RANK_NAMES[$i - 1],
                    'min_spend' => $prevThreshold,
                    'benefits' => self::rankBenefits(self::RANK_NAMES[$i - 1]),
                    'all_ranks' => self::RANK_NAMES,
                    'next_name' => $name,
                    'next_min' => $threshold,
                ];
            }
        }
        // Melebihi semua rank — Legend++ (semua rank habis, create custom)
        $lastRank = end(self::RANK_NAMES);
        $lastThreshold = 5000000 * pow(2, count(self::RANK_NAMES) - 2);
        return [
            'name' => $lastRank,
            'label' => $lastRank,
            'min_spend' => $lastThreshold,
            'benefits' => 'Rank tertinggi tanpa batas!',
            'all_ranks' => self::RANK_NAMES,
            'next_name' => null,
            'next_min' => null,
        ];
    }

    /**
     * Hitung kredit yang diperoleh (flat rate, tanpa multiplier rank)
     */
    public function calculateCredits(float $amount): int
    {
        return (int)($amount / self::CREDIT_PER_RUPIAH);
    }

    /**
     * Tambah kredit ke customer (dari order selesai)
     */
    public function earnCredits(Customer $customer, float $orderAmount, string $referenceType, int $referenceId): WholesaleCreditLog
    {
        $credits = $this->calculateCredits($orderAmount);

        // Update customer totals
        $customer->increment('total_credits_earned', $credits);
        $customer->increment('lifetime_spend', $orderAmount);

        // Cek rank up (berdasarkan lifetime_spend, bukan credits)
        $this->checkRankUp($customer);

        return WholesaleCreditLog::create([
            'customer_id' => $customer->id,
            'credits' => $credits,
            'gold_points' => 0,
            'type' => 'earn',
            'description' => 'Kredit dari pesanan #' . $referenceId . ' — Rp ' . number_format($orderAmount, 0, ',', '.'),
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
        ]);
    }

    /**
     * Cek dan naikkan rank — tanpa batas, terus naik
     */
    public function checkRankUp(Customer $customer): bool
    {
        $currentName = $customer->loyalty_rank ?: 'Regular';
        $spend = $customer->lifetime_spend;
        $rankInfo = self::getRank($spend);
        $newName = $rankInfo['name'];

        if ($newName !== $currentName) {
            $customer->update(['loyalty_rank' => $newName]);
            WholesaleCreditLog::create([
                'customer_id' => $customer->id,
                'credits' => 0,
                'gold_points' => 0,
                'type' => 'rank_up',
                'description' => 'Naik rank ke ' . $newName . '!',
            ]);
            return true;
        }
        return false;
    }

    /**
     * Redeem kredit — dipakai sekali, kredit reset ke 0, rank tetap
     */
    public function redeemCredits(Customer $customer, WholesaleRedemption $redemption, ?float $currentOrderAmount = null): array
    {
        $availableCredits = $customer->total_credits_earned - $customer->total_credits_spent;

        if ($availableCredits < $redemption->credits_required) {
            return ['success' => false, 'message' => 'Kredit tidak mencukupi.'];
        }

        // Cek batas penggunaan per transaksi: max floor(amount / 2.000.000)
        if ($currentOrderAmount !== null) {
            $maxPerTransaction = (int)($currentOrderAmount / 2000000);
            if ($maxPerTransaction < 1) {
                return ['success' => false, 'message' => 'Minimal transaksi Rp2.000.000 untuk menggunakan kredit.'];
            }
            if ($redemption->credits_required > $maxPerTransaction) {
                return ['success' => false, 'message' => 'Batas penggunaan kredit untuk transaksi ini hanya ' . $maxPerTransaction . ' kredit.'];
            }
        }

        if ($redemption->max_uses_per_customer > 0) {
            $used = WholesaleCustomerRedemption::where('customer_id', $customer->id)
                ->where('redemption_id', $redemption->id)
                ->count();
            if ($used >= $redemption->max_uses_per_customer) {
                return ['success' => false, 'message' => 'Batas penggunaan promo ini sudah tercapai.'];
            }
        }

        // Simpan redemption
        $customer->increment('total_credits_spent', $redemption->credits_required);

        WholesaleCustomerRedemption::create([
            'customer_id' => $customer->id,
            'redemption_id' => $redemption->id,
            'credits_spent' => $redemption->credits_required,
            'status' => 'used',
            'used_at' => now(),
        ]);

        // Reset kredit ke 0 (total_credits_earned = total_credits_spent)
        // Rank tetap tinggi (tidak di-reset)
        $customer->update([
            'total_credits_earned' => $customer->total_credits_spent,
        ]);

        WholesaleCreditLog::create([
            'customer_id' => $customer->id,
            'credits' => -$redemption->credits_required,
            'gold_points' => 0,
            'type' => 'spend',
            'description' => 'Tukar promo: ' . $redemption->name . ' — skor kredit reset',
            'reference_type' => 'redemption',
            'reference_id' => $redemption->id,
        ]);

        WholesaleCreditLog::create([
            'customer_id' => $customer->id,
            'credits' => 0,
            'gold_points' => 0,
            'type' => 'earn',
            'description' => 'Reset skor kredit — rank ' . $customer->loyalty_rank . ' tetap',
        ]);

        return ['success' => true, 'message' => $redemption->name . ' berhasil ditukar! Skor kredit di-reset, rank tetap ' . $customer->loyalty_rank . '.'];
    }

    /**
     * Dapatkan info rank untuk customer
     */
    public function getRankInfo(Customer $customer): array
    {
        $spend = $customer->lifetime_spend;
        $rankInfo = self::getRank($spend);
        $availableCredits = $this->getAvailableCredits($customer);

        $progress = 0;
        if ($rankInfo['next_name'] && $rankInfo['next_min'] > $rankInfo['min_spend']) {
            $progress = min(100, (($spend - $rankInfo['min_spend']) / ($rankInfo['next_min'] - $rankInfo['min_spend'])) * 100);
        }

        return [
            'current_rank' => $rankInfo['name'],
            'benefits' => $rankInfo['benefits'],
            'available_credits' => $availableCredits,
            'gold_points' => $customer->gold_points,
            'is_top_rank' => $rankInfo['next_name'] === null,
            'next_rank' => $rankInfo['next_name'] ? ['name' => $rankInfo['next_name'], 'min_spend' => $rankInfo['next_min']] : null,
            'progress' => $progress,
        ];
    }

    /**
     * Benefits per rank (bukan multiplier, tapi fitur berbeda)
     */
    private static function rankBenefits(string $rank): string
    {
        return match ($rank) {
            'Regular' => 'Kredit dasar',
            'Bronze' => 'Kredit + prioritas ringan',
            'Silver' => 'Kredit + gratis ongkir 1x/bulan',
            'Gold' => 'Kredit + gratis ongkir + prioritas proses',
            'Platinum' => 'Kredit + gratis ongkir + prioritas + undangan launching',
            'Diamond' => 'Kredit + gratis ongkir + prioritas + diskon 5%',
            'Exclusive' => 'Kredit + gratis ongkir + prioritas + diskon 10%',
            'Premium' => 'Kredit + gratis ongkir + prioritas + diskon 15%',
            'Elite' => 'Kredit + gratis ongkir + prioritas + diskon 20%',
            'Royal' => 'Kredit + gratis ongkir + prioritas + diskon 25%',
            'Imperial' => 'Kredit + gratis ongkir + prioritas + diskon 30%',
            'Legend' => 'Kredit + gratis ongkir + prioritas + diskon 35%',
            'Mythic' => 'Kredit + gratis ongkir + prioritas + diskon 40%',
            'Transcend' => 'Kredit + gratis ongkir + prioritas + diskon 45%',
            'Infinite' => 'Kredit + gratis ongkir + prioritas + diskon 50% + VIP Gold',
            default => 'Kredit istimewa',
        };
    }

    public function getAvailableCredits(Customer $customer): int
    {
        return $customer->total_credits_earned - $customer->total_credits_spent;
    }
}
