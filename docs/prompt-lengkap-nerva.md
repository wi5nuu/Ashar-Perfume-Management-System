# 🚀 NERVA — AI Agent Blueprint: Android WebView App with VSCode

> **Target:** Membangun aplikasi Android WebView wrapper dari nol hingga APK signed siap distribusi, menggunakan **Kotlin + VSCode (100% CLI-based, NO Android Studio)**.
>
> **Audience:** AI Agent (LLM) yang akan menulis kode, menjalankan perintah, dan menghasilkan APK secara mandiri.

---

## 📋 MASTER TABLE OF CONTENTS

| # | Section | AI Actionability |
|---|---------|------------------|
| 1 | [KONSEP & ARSITEKTUR](#1-konsep--arsitektur) | 🧠 Pahami dulu |
| 2 | [UI/UX DESIGN SYSTEM](#2-uiux-design-system) | 🎨 Referensi warna/tema |
| 3 | [PERSIAPAN ENVIRONMENT VScode](#3-persiapan-environment-vscode) | ⚙️ Setup tools |
| 4 | [STRUKTUR PROJECT](#4-struktur-project) | 📁 Buat file/folder |
| 5 | [GRADLE MODERN (Version Catalog)](#5-gradle-modern-version-catalog) | 🛠️ Build config |
| 6 | [ANDROIDMANIFEST.XML](#6-androidmanifestxml) | 📜 Izin & component |
| 7 | [NETWORK SECURITY](#7-network-security) | 🔐 Keamanan koneksi |
| 8 | [SPLASH SCREEN](#8-splash-screen) | 🖼️ Launch screen |
| 9 | [LAYOUT: activity_main.xml](#9-layout-activity_mainxml) | 🎭 XML layout |
| 10 | [MAINACTIVITY.KT — Full Code](#10-mainactivitykt--full-code) | 💎 Kode utama |
| 11 | [WEBVIEW ENGINEERING](#11-webview-engineering) | ⚡ Konfigurasi lanjutan |
| 12 | [RESOURCES & ASSETS](#12-resources--assets) | 📦 File resource |
| 13 | [GENERATE ICON & ASSETS](#13-generate-icon--assets) | 🎯 CLI-based icon |
| 14 | [BUILD CONFIG & FLAVORS](#14-build-config--flavors) | 🔧 variant management |
| 15 | [R8 / PROGUARD](#15-r8--proguard) | 🗜️ Code shrinking |
| 16 | [TESTING STRATEGY](#16-testing-strategy) | 🧪 Validation |
| 17 | [BUILD APK (CLI)](#17-build-apk-cli) | 📦 Generate APK |
| 18 | [CODE SIGNING](#18-code-signing) | ✍️ Signing release |
| 19 | [CI/CD PIPELINE](#19-cicd-pipeline) | 🤖 GitHub Actions |
| 20 | [DISTRIBUSI & UPDATE](#20-distribusi--update) | 📤 Ship to users |
| 21 | [FITUR ENTERPRISE](#21-fitur-enterprise) | 🏢 Advanced features |
| 22 | [TROUBLESHOOTING](#22-troubleshooting) | 🔍 Debug guide |
| 23 | [AI AGENT INSTRUCTIONS](#23-ai-agent-instructions) | 🤖 Directives for LLM |

---

## 1. KONSEP & ARSITEKTUR

### 1.1 Apa Itu Nerva?

**Nerva** adalah aplikasi Android **hybrid WebView wrapper** — membungkus web app manajemen bisnis ke dalam shell Android native. Konsep:

- **Gojek / Tokopedia** — konten dari server, dibungkus native
- Bedanya: Nerva menggunakan **WebView** (Chromium engine) untuk render web app dengan akses penuh ke session, cookie, localStorage, dan bridge ke Android native API.

### 1.2 Arsitektur Sistem

```
┌──────────────────────────────────────────────────────────────────────┐
│                      ANDROID APP (NERVA APK)                         │
│                                                                      │
│  ┌──────────┐    ┌──────────────┐    ┌───────────────────────────┐   │
│  │ Splash   │───▶│ MainActivity │───▶│        WebView            │   │
│  │ Screen   │    │ (Kotlin)     │    │  ┌─────────────────────┐  │   │
│  └──────────┘    └──────┬───────┘    │  │  Web App (HTML)     │  │   │
│                         │            │  │  + CSS + JS         │  │   │
│         ┌────────────────┴──────┐    │  │  + Vue/React        │  │   │
│         │  JavaScript Bridge    │◀──▶│  │  + Laravel API      │  │   │
│         │  (NervaBridge.kt)     │    │  └─────────┬───────────┘  │   │
│         └───────────────────────┘    └────────────┼──────────────┘   │
│                                                    │ HTTPS/WSS       │
│  ┌────────────────────────────────────────┐        │                 │
│  │  Cookie / Session / localStorage       │◀───────┘                 │
│  └────────────────────────────────────────┘                          │
└──────────────────────────────────────────────────────┼───────────────┘
                                                       │
                                          ┌────────────▼──────────────┐
                                          │     WEB SERVER            │
                                          │  (Laravel / Nginx)        │
                                          │  /nerva/*                 │
                                          │  API REST / JSON          │
                                          │  Database (MySQL/Postgres)│
                                          └───────────────────────────┘
```

### 1.3 Data Flow Pipeline

```
[User Tap] → [Android Touch Event] → [WebView Chromium Engine]
    → [JavaScript Event Handler] → [fetch/Ajax → HTTPS]
    → [Web Server (Laravel)] → [Query DB] → [Response JSON/HTML]
    → [WebView Render DOM] → [CSS Layout → Paint → Display]
```

### 1.4 Decision Matrix: WebView vs Native

| Kriteria | WebView (Nerva) | Native Kotlin |
|----------|-----------------|---------------|
| Time-to-Market | **1-2 hari** | 2-6 bulan |
| APK Size | **3-8 MB** | 20-100 MB |
| Update Frekuensi | **Instant** (deploy server) | via Play Store |
| Performa | Tergantung koneksi | Native cepat |
| Offline | ❌ Butuh internet | ✅ Bisa lokal |
| Multi-platform | Android + iOS via WebView | Harus ulang |
| Hardware Access | Terbatas (via JS Bridge) | Full native |
| Maintenance Cost | Rendah (1 codebase) | Tinggi |

### 1.5 Kapan WebView Tepat?

✅ **COCOK:**
- App internal perusahaan / B2B
- Update fitur frequent (harian/mingguan)
- Data realtime dari server
- Tim kecil dengan resource terbatas
- Prototype / MVP cepat

❌ **TIDAK COCOK:**
- Game, video editor, heavy computation
- Offline-first aplikasi
- Butuh akses hardware intensif (kamera realtime, AR)
- Target pengguna dengan koneksi tidak stabil

### 1.6 Application State Machine (Formal)

```
states:
  [SPLASH] → [CHECK_NETWORK] → [LOADING] → [READY] → [ERROR]
                                                  ↓
                                             [RETRY]

transitions:
  SPLASH ──(1.5s timeout)──▶ CHECK_NETWORK
  CHECK_NETWORK ──(online)──▶ LOADING
  CHECK_NETWORK ──(offline)──▶ ERROR
  LOADING ──(success)──▶ READY
  LOADING ──(fail)──▶ ERROR
  ERROR ──(retry tap)──▶ CHECK_NETWORK
  READY ──(connection lost)──▶ ERROR
  READY ──(back, no history)──▶ DOUBLE_TAP_EXIT
  DOUBLE_TAP_EXIT ──(2s window)──▶ FINISH
```

---

## 2. UI/UX DESIGN SYSTEM

### 2.1 Color Tokens

| Token | Hex | Usage |
|-------|-----|-------|
| `nerva_primary` | `#FF6B35` | Tombol, link, accent utama |
| `nerva_primary_dark` | `#E55A2B` | Status bar, shadow |
| `nerva_dark` | `#1A1A2E` | Background utama (navy) |
| `nerva_dark_blue` | `#16213E` | Card, surface |
| `nerva_accent` | `#0F3460` | Elemen sekunder |
| `nerva_success` | `#43A047` | Sukses, verified |
| `nerva_error` | `#E53935` | Error, gagal |
| `nerva_warning` | `#FB8C00` | Warning |
| `nerva_info` | `#1E88E5` | Info |
| `nerva_gold` | `#F59E0B` | Premium, reward |
| `text_primary` | `#FFFFFF` | Teks utama (dark bg) |
| `text_secondary` | `#9E9E9E` | Teks sekunder |

### 2.2 Typography

```css
/* System font stack — mengikuti OS */
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
             Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;

/* Scale */
--text-headline: 24px Bold;
--text-title:    18px Bold;
--text-body:     14px Regular;
--text-caption:  12px Regular;
--text-small:    11px Medium;
```

### 2.3 Spacing Scale (4px base)

```css
--space-1: 4px;   /* icon ke teks */
--space-2: 8px;   /* antar elemen card */
--space-3: 12px;  /* antar section */
--space-4: 16px;  /* margin card ke tepi */
--space-5: 20px;  /* antar card */
--space-6: 24px;  /* antar halaman */
--space-8: 32px;  /* section hero */
```

### 2.4 Component Specifications

**Splash Screen:**
- Background: `#1A1A2E` (solid)
- Logo: center, `@mipmap/ic_launcher`
- Durasi: 1.5 detik atau sampai WebView siap

**WebView Container:**
- Full screen, tanpa ActionBar
- Status bar: `#1A1A2E`, light icons
- Navigation bar: `#1A1A2E`
- Progress bar: 3dp, `#FF6B35`, top

**Error Page (custom HTML):**
- Background: `#1A1A2E`
- Icon: 📡 (72px)
- Title: `#FF6B35`, 22px Bold
- Message: `#9E9E9E`, 14px
- Button primary: `#FF6B35` bg, 14px padding, radius 10px

### 2.5 Complete User Flow

```
─── INSTALL ───
Download APK → Install (unknown sources) → Tap Icon

─── LAUNCH ───
Splash Screen (1.5s) → Cek Internet
  ├── ONLINE → WebView load HOME_URL → Halaman Login
  └── OFFLINE → Error Page with Retry
       └── Tap Retry → Cek ulang → ONLINE? → Load URL

─── AUTH ───
Login Form → Submit → POST ke Server
  ├── 200 OK → Set-Cookie → Dashboard
  ├── 401 → Error message di form
  └── Timeout → Error Page

─── NAVIGATION ───
Back gesture/fisik:
  ├── WebView.canGoBack() == true → goBack()
  └── WebView.canGoBack() == false
       ├── Double tap dalam 2 detik → finish()
       └── Tap pertama → Toast "Tekan sekali lagi"

─── CONNECTION LOST ───
Online → Tiba-tiba offline:
  → WebView onReceivedError → Error Page
  → Koneksi kembali (NetworkCallback.onAvailable)
  → Auto reload → READY

─── EXIT ───
finish() → Activity destroyed → WebView memory cleanup
```

---

## 3. PERSIAPAN ENVIRONMENT VScode

### 3.1 Prasyarat System

| Software | Version Min | Fungsi | Install Command (Linux/WSL) |
|----------|-------------|--------|---------------------------|
| **OpenJDK** | 17 LTS | Compiler Kotlin/Java | `sudo apt install openjdk-17-jdk` |
| **Android SDK** | 34 | Android library & tools | via `sdkmanager` (CLI) |
| **Gradle** | 8.5+ | Build system | Via wrapper (`gradlew`) — no manual install |
| **VSCode** | Latest | Code editor | `sudo snap install code` |
| **Kotlin Plugin** | Latest | Language support | VSCode extension |
| **Android Extensions** | Latest | Android support | VSCode extension pack |

### 3.2 Setup Android SDK (CLI Only — No Android Studio)

```bash
# 1. Download command-line tools (NOT Android Studio)
# URL: https://developer.android.com/studio#command-line-tools-only
wget https://dl.google.com/android/repository/commandlinetools-linux-11076708_latest.zip
unzip commandlinetools-linux-*.zip -d ~/Android
mkdir -p ~/Android/cmdline-tools/latest
mv ~/Android/cmdline-tools/* ~/Android/cmdline-tools/latest/ 2>/dev/null || true

# 2. Set environment variables (add to ~/.bashrc or ~/.zshrc)
export ANDROID_HOME=$HOME/Android
export PATH=$PATH:$ANDROID_HOME/cmdline-tools/latest/bin
export PATH=$PATH:$ANDROID_HOME/platform-tools

# 3. Accept licenses & install SDK
yes | sdkmanager --licenses
sdkmanager "platforms;android-34" "build-tools;34.0.0" "platform-tools" "emulator"

# 4. Verify
java -version
sdkmanager --list --verbose 2>/dev/null | head -20
```

### 3.3 VSCode Extensions Wajib

Create `.vscode/extensions.json`:

```json
{
  "recommendations": [
    "mathiasfrohlich.Kotlin",
    "fwcd.kotlin",
    "vscjava.vscode-java-pack",
    "naco-siren.gradle-language",
    "richardwillis.vscode-gradle-extension",
    "github.vscode-github-actions",
    "eamodio.gitlens",
    "aaron-bond.better-comments",
    "streetsidesoftware.code-spell-checker"
  ]
}
```

### 3.4 VSCode Workspace Settings

Create `.vscode/settings.json`:

```json
{
  "java.configuration.updateBuildConfiguration": "automatic",
  "java.compile.nullAnalysis.mode": "automatic",
  "kotlin.compiler.jvmTarget": "17",
  "files.exclude": {
    "**/.gradle": true,
    "**/build": true,
    "**/.idea": true
  },
  "editor.formatOnSave": true,
  "editor.codeActionsOnSave": {
    "source.organizeImports": "explicit"
  },
  "[kotlin]": {
    "editor.tabSize": 4,
    "editor.defaultFormatter": "mathiasfrohlich.Kotlin"
  },
  "[xml]": {
    "editor.tabSize": 4
  },
  "gradle.nestedProjects": true
}
```

### 3.5 VSCode Tasks for Build

Create `.vscode/tasks.json`:

```json
{
  "version": "2.0.0",
  "tasks": [
    {
      "label": "Build Debug APK",
      "type": "shell",
      "command": "./gradlew assembleDebug",
      "group": "build",
      "problemMatcher": ["$gradle"],
      "presentation": { "echo": true, "reveal": "always", "panel": "dedicated" }
    },
    {
      "label": "Build Release APK",
      "type": "shell",
      "command": "./gradlew assembleRelease",
      "group": "build",
      "problemMatcher": ["$gradle"]
    },
    {
      "label": "Clean Project",
      "type": "shell",
      "command": "./gradlew clean",
      "group": "build"
    },
    {
      "label": "Run Lint",
      "type": "shell",
      "command": "./gradlew lint",
      "group": "build",
      "problemMatcher": ["$gradle"]
    },
    {
      "label": "Run Tests",
      "type": "shell",
      "command": "./gradlew test",
      "group": "test"
    },
    {
      "label": "Install Debug APK to Device",
      "type": "shell",
      "command": "./gradlew installDebug",
      "group": "build"
    }
  ]
}
```

### 3.6 VSCode Launch Config (Debug)

Create `.vscode/launch.json`:

```json
{
  "version": "0.2.0",
  "configurations": [
    {
      "type": "android",
      "request": "attach",
      "name": "Debug Android App",
      "host": "localhost",
      "port": 8700,
      "sourcePaths": ["app/src/main/java"],
      "adbPort": 5037
    }
  ]
}
```

### 3.7 Buat Project (CLI — No Android Studio Wizard)

```bash
# Step 1: Buat folder project & init git
mkdir Nerva && cd Nerva
git init

# Step 2: Buat struktur folder manual
mkdir -p app/src/main/java/com/nerva/app
mkdir -p app/src/main/res/layout
mkdir -p app/src/main/res/values
mkdir -p app/src/main/res/drawable
mkdir -p app/src/main/res/xml
mkdir -p app/src/main/res/mipmap-hdpi
mkdir -p app/src/main/res/mipmap-mdpi
mkdir -p app/src/main/res/mipmap-xhdpi
mkdir -p app/src/main/res/mipmap-xxhdpi
mkdir -p app/src/main/res/mipmap-xxxhdpi
mkdir -p app/src/main/res/mipmap-anydpi-v26
mkdir -p app/src/test/java/com/nerva/app

# Step 3: Download Gradle wrapper directly (no Gradle installation needed)
# Get the wrapper jar from a known working project or use curl
curl -L -o gradle/wrapper/gradle-wrapper.jar \
  "https://raw.githubusercontent.com/gradle/gradle/v8.5.0/gradle/wrapper/gradle-wrapper.jar"

# Step 4: Create gradle-wrapper.properties
mkdir -p gradle/wrapper
cat > gradle/wrapper/gradle-wrapper.properties << 'EOF'
distributionBase=GRADLE_USER_HOME
distributionPath=wrapper/dists
distributionUrl=https\://services.gradle.org/distributions/gradle-8.5-bin.zip
networkTimeout=10000
validateDistributionUrl=true
zipStoreBase=GRADLE_USER_HOME
zipStorePath=wrapper/dists
EOF

# Step 5: Create gradlew script
cat > gradlew << 'SCRIPT'
#!/bin/sh
# Gradle wrapper script
APP_NAME="Gradle"
APP_BASE_NAME=$(basename "$0")
DEFAULT_JVM_OPTS='"-Xmx64m" "-Xms64m"'
DIRNAME=$(dirname "$0")
APP_HOME=$(cd "$DIRNAME" && pwd)
CLASSPATH=$APP_HOME/gradle/wrapper/gradle-wrapper.jar
exec java $DEFAULT_JVM_OPTS -classpath "$CLASSPATH" org.gradle.wrapper.GradleWrapperMain "$@"
SCRIPT
chmod +x gradlew

# Step 6: Create gradlew.bat for Windows
cat > gradlew.bat << 'SCRIPT'
@rem Gradle wrapper script for Windows
@if "%DEBUG%"=="" @echo off
set DIRNAME=%~dp0
set APP_HOME=%DIRNAME%
set CLASSPATH=%APP_HOME%\gradle\wrapper\gradle-wrapper.jar
"%JAVA_HOME%/bin/java.exe" -classpath "%CLASSPATH%" org.gradle.wrapper.GradleWrapperMain %*
SCRIPT

# Step 7: Create local.properties with SDK path
echo "sdk.dir=$HOME/Android" > local.properties

# Step 8: Verify
ls -la
./gradlew --version
```

> **AI Agent Note:** Seluruh setup di atas 100% CLI. Tidak ada Android Studio GUI wizard. Semua file konfigurasi ditulis manual. Gradle wrapper di-bootstrap via download langsung JAR dari GitHub, tidak perlu Gradle terinstall.

---

## 4. STRUKTUR PROJECT

### 4.1 Complete File Tree

```
Nerva/
├── .gitignore
├── .gitattributes
├── .editorconfig
├── local.properties              ← Auto-generated (SDK path)
├── gradle.properties             ← Gradle JVM & Android configs
├── gradlew                      ← Gradle wrapper (Linux/Mac)
├── gradlew.bat                  ← Gradle wrapper (Windows)
├── gradle/
│   ├── wrapper/
│   │   ├── gradle-wrapper.jar
│   │   └── gradle-wrapper.properties
│   └── libs.versions.toml       ← VERSION CATALOG (modern)
│
├── build.gradle.kts             ← Project-level (root)
├── settings.gradle.kts          ← Project settings
│
├── .vscode/                     ← VSCode configuration
│   ├── extensions.json
│   ├── settings.json
│   ├── tasks.json
│   └── launch.json
│
├── app/
│   ├── build.gradle.kts         ← App-level (main config)
│   ├── proguard-rules.pro       ← R8/ProGuard rules
│   └── src/
│       ├── main/
│       │   ├── AndroidManifest.xml
│       │   ├── java/com/nerva/app/
│       │   │   └── MainActivity.kt    ← Single Kotlin file
│       │   └── res/
│       │       ├── drawable/
│       │       │   ├── splash_background.xml
│       │       │   └── ic_launcher_foreground.xml
│       │       ├── drawable-v24/
│       │       │   └── ic_launcher_foreground.xml
│       │       ├── layout/
│       │       │   └── activity_main.xml
│       │       ├── mipmap-hdpi/       (72x72)
│       │       ├── mipmap-mdpi/       (48x48)
│       │       ├── mipmap-xhdpi/      (96x96)
│       │       ├── mipmap-xxhdpi/     (144x144)
│       │       ├── mipmap-xxxhdpi/    (192x192)
│       │       ├── mipmap-anydpi-v26/ (adaptive icon)
│       │       ├── values/
│       │       │   ├── colors.xml
│       │       │   ├── strings.xml
│       │       │   └── themes.xml
│       │       └── xml/
│       │           └── network_security_config.xml
│       └── test/
│           └── java/com/nerva/app/
│               └── ExampleUnitTest.kt
│
├── docs/
│   └── prompt-lengkap-nerva.md  ← THIS FILE
│
└── scripts/                     ← Utility scripts (opsional)
    ├── make-icons.sh
    └── bump-version.sh
```

### 4.2 Key Files & Purpose

| File | Purpose |
|------|---------|
| `gradle/libs.versions.toml` | Version catalog — single source of truth untuk dependency versions |
| `.vscode/tasks.json` | Build/run/deploy tasks untuk VSCode |
| `app/build.gradle.kts` | Build config: SDK versions, signing, build types |
| `AndroidManifest.xml` | Izin, component, intent filters |
| `MainActivity.kt` | Satu-satunya Activity — semua logika WebView |
| `network_security_config.xml` | HTTP/HTTPS domain whitelist |

---

## 5. GRADLE MODERN (Version Catalog)

### 5.1 Version Catalog: `gradle/libs.versions.toml`

Modern Android projects use **Version Catalog** (`libs.versions.toml`) for centralized dependency management. All version numbers live here.

```toml
[versions]
agp = "8.2.2"
kotlin = "1.9.22"
core-ktx = "1.12.0"
appcompat = "1.6.1"
swiperefreshlayout = "1.1.0"
junit = "4.13.2"
activity = "1.8.2"

[libraries]
core-ktx = { group = "androidx.core", name = "core-ktx", version.ref = "core-ktx" }
appcompat = { group = "androidx.appcompat", name = "appcompat", version.ref = "appcompat" }
swiperefreshlayout = { group = "androidx.swiperefreshlayout", name = "swiperefreshlayout", version.ref = "swiperefreshlayout" }
activity-ktx = { group = "androidx.activity", name = "activity-ktx", version.ref = "activity" }
junit = { group = "junit", name = "junit", version.ref = "junit" }

[plugins]
android-application = { id = "com.android.application", version.ref = "agp" }
kotlin-android = { id = "org.jetbrains.kotlin.android", version.ref = "kotlin" }
```

### 5.2 `build.gradle.kts` (Project Level)

```kotlin
plugins {
    alias(libs.plugins.android.application) apply false
    alias(libs.plugins.kotlin.android) apply false
}
```

### 5.3 `settings.gradle.kts`

```kotlin
pluginManagement {
    repositories {
        google()
        mavenCentral()
        gradlePluginPortal()
    }
}

dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        google()
        mavenCentral()
    }
}

rootProject.name = "Nerva"
include(":app")
```

### 5.4 `gradle.properties`

```properties
# JVM
org.gradle.jvmargs=-Xmx4g -XX:MaxMetaspaceSize=512m -XX:+HeapDumpOnOutOfMemoryError
org.gradle.parallel=true
org.gradle.caching=true
org.gradle.configuration-cache=true
org.gradle.configuration-cache.problems=warn

# Android
android.useAndroidX=true
android.nonTransitiveRClass=true
android.enableJetifier=false

# Kotlin
kotlin.code.style=official
kotlin.daemon.jvmargs=-Xmx2g

# Build Cache
org.gradle.caching.debug=false
```

### 5.5 `app/build.gradle.kts` (App Level) — Full Config

```kotlin
plugins {
    alias(libs.plugins.android.application)
    alias(libs.plugins.kotlin.android)
}

android {
    namespace = "com.nerva.app"
    compileSdk = 34

    defaultConfig {
        applicationId = "com.nerva.app"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0.0"

        // ⚙️ URL Config — accessible via BuildConfig.HOME_URL
        buildConfigField("String", "HOME_URL", "\"http://10.0.2.2/nerva\"")
        buildConfigField("String", "APP_VERSION", "\"1.0.0\"")
        buildConfigField("Boolean", "IS_DEBUG", "true")
    }

    // 🔐 Signing Config — loaded from keystore.properties (NOT in VCS)
    val keystoreFile = rootProject.file("keystore.properties")
    if (keystoreFile.exists()) {
        val keystoreProps = java.util.Properties().apply {
            load(keystoreFile.inputStream())
        }
        signingConfigs {
            create("release") {
                storeFile = rootProject.file(keystoreProps["storeFile"]!!)
                storePassword = keystoreProps["storePassword"] as String
                keyAlias = keystoreProps["keyAlias"] as String
                keyPassword = keystoreProps["keyPassword"] as String
            }
        }
    }

    buildTypes {
        debug {
            isDebuggable = true
            isMinifyEnabled = false
            applicationIdSuffix = ".debug"
            versionNameSuffix = "-debug"
            signingConfig = signingConfigs.getByName("debug")
        }

        release {
            isDebuggable = false
            isMinifyEnabled = true
            isShrinkResources = true
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
            signingConfig = if (keystoreFile.exists()) {
                signingConfigs.getByName("release")
            } else {
                signingConfigs.getByName("debug")
            }
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    kotlinOptions {
        jvmTarget = "17"
    }

    buildFeatures {
        buildConfig = true
    }

    // 🎨 Vector drawable support for older devices
    vectorDrawables {
        useSupportLibrary = true
    }

    // ⚡ Slow whole document draw fix (prevent WebView white flash on slow pages)
    packaging {
        jniLibs {
            useLegacyPackaging = true
        }
    }

    lint {
        disable += "MissingApplicationIcon"
        abortOnError = false
    }

    packaging {
        resources {
            excludes += "/META-INF/{AL2.0,LGPL2.1}"
        }
    }
}

dependencies {
    implementation(libs.core.ktx)
    implementation(libs.appcompat)
    implementation(libs.swiperefreshlayout)
    implementation(libs.activity.ktx)

    testImplementation(libs.junit)
}
```

### 5.6 Keystore Management (Enterprise)

File `keystore.properties` — **TIDAK boleh di-commit ke git**:

```properties
storeFile=../nerva-keystore.jks
storePassword=your-strong-password-here
keyAlias=nerva
keyPassword=your-key-password-here
```

Add to `.gitignore`:
```
keystore.properties
*.jks
*.keystore
local.properties
```

---

## 6. ANDROIDMANIFEST.XML

```xml
<?xml version="1.0" encoding="utf-8"?>
<manifest xmlns:android="http://schemas.android.com/apk/res/android"
    xmlns:tools="http://schemas.android.com/tools">

    <!-- 🌐 Permission -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />
    <uses-permission android:name="android.permission.ACCESS_WIFI_STATE"
        android:maxSdkVersion="28" />
    <uses-permission android:name="android.permission.POST_NOTIFICATIONS" />

    <application
        android:allowBackup="true"
        android:icon="@mipmap/ic_launcher"
        android:label="@string/app_name"
        android:roundIcon="@mipmap/ic_launcher_round"
        android:supportsRtl="true"
        android:theme="@style/Theme.Nerva"
        android:usesCleartextTraffic="true"
        android:networkSecurityConfig="@xml/network_security_config"
        tools:targetApi="34">

        <activity
            android:name=".MainActivity"
            android:exported="true"
            android:configChanges="orientation|screenSize|screenLayout|keyboardHidden|keyboard"
            android:windowSoftInputMode="adjustResize"
            android:theme="@style/Theme.Nerva.Splash">

            <intent-filter>
                <action android:name="android.intent.action.MAIN" />
                <category android:name="android.intent.category.LAUNCHER" />
            </intent-filter>

            <!-- Deep Linking -->
            <intent-filter android:autoVerify="true">
                <action android:name="android.intent.action.VIEW" />
                <category android:name="android.intent.category.DEFAULT" />
                <category android:name="android.intent.category.BROWSABLE" />
                <data android:scheme="https" android:host="nerva.app" />
                <data android:scheme="http" android:host="nerva.app" />
            </intent-filter>
        </activity>

    </application>
</manifest>
```

### 6.1 Permission Justification

| Permission | Why | Risk if Removed |
|------------|-----|-----------------|
| `INTERNET` | WebView loading | App blank/error |
| `ACCESS_NETWORK_STATE` | Detect connectivity | Tidak bisa deteksi offline |
| `POST_NOTIFICATIONS` | Push notification (Android 13+) | Notif tidak muncul |

---

## 7. NETWORK SECURITY

### 7.1 `res/xml/network_security_config.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<network-security-config>
    <!-- Development: allow HTTP to local network -->
    <domain-config cleartextTrafficPermitted="true">
        <domain includeSubdomains="true">10.0.2.2</domain>
        <domain includeSubdomains="true">localhost</domain>
        <domain includeSubdomains="true">192.168.0.0/16</domain>
        <domain includeSubdomains="true">172.16.0.0/12</domain>
        <domain includeSubdomains="true">10.0.0.0/8</domain>
    </domain-config>

    <!-- Production: HTTPS only -->
    <base-config cleartextTrafficPermitted="false">
        <trust-anchors>
            <certificates src="system" />
        </trust-anchors>
    </base-config>
</network-security-config>
```

### 7.2 SSL Handling Strategy

| Environment | `onReceivedSslError` | `network_security_config` |
|-------------|---------------------|--------------------------|
| Development | `handler.proceed()` | Allow HTTP to local IPs |
| Production | `handler.cancel()` | HTTPS only, pinning optional |

---

## 8. SPLASH SCREEN

### 8.1 Theme-Based Splash (Recommended — 0ms delay, no white flash)

**`res/values/themes.xml`:**
```xml
<resources>
    <style name="Theme.Nerva" parent="Theme.AppCompat.DayNight.NoActionBar">
        <item name="colorPrimary">@color/nerva_primary</item>
        <item name="colorPrimaryDark">@color/nerva_primary_dark</item>
        <item name="colorAccent">@color/nerva_primary</item>
        <item name="android:statusBarColor">@color/nerva_dark</item>
        <item name="android:navigationBarColor">@color/nerva_dark</item>
        <item name="android:windowBackground">@color/nerva_dark</item>
        <item name="android:windowLightStatusBar">false</item>
    </style>

    <style name="Theme.Nerva.Splash" parent="Theme.Nerva">
        <item name="android:windowBackground">@drawable/splash_background</item>
    </style>
</resources>
```

**`res/drawable/splash_background.xml`:**
```xml
<?xml version="1.0" encoding="utf-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
    <item android:drawable="@color/nerva_dark" />
    <item
        android:drawable="@mipmap/ic_launcher"
        android:gravity="center" />
</layer-list>
```

**In `AndroidManifest.xml`:**
```xml
<activity
    android:name=".MainActivity"
    android:theme="@style/Theme.Nerva.Splash"
    ...>
```

**In `MainActivity.kt`:**
```kotlin
override fun onCreate(savedInstanceState: Bundle?) {
    setTheme(R.style.Theme.Nerva)  // ⚡ Revert BEFORE super.onCreate
    super.onCreate(savedInstanceState)
    // ...
}
```

### 8.2 Why Theme-Based?

| Method | White Flash | Complexity | Compatibility |
|--------|------------|------------|---------------|
| Theme-Based (windowBackground) | ❌ None | Low | All versions |
| SplashScreen API (Android 12+) | Minimal | Medium | API 31+ |
| Custom Activity | High | High | All versions |

---

## 9. LAYOUT: activity_main.xml

```xml
<?xml version="1.0" encoding="utf-8"?>
<LinearLayout xmlns:android="http://schemas.android.com/apk/res/android"
    android:layout_width="match_parent"
    android:layout_height="match_parent"
    android:orientation="vertical"
    android:background="@color/nerva_dark">

    <ProgressBar
        android:id="@+id/progressBar"
        style="?android:attr/progressBarStyleHorizontal"
        android:layout_width="match_parent"
        android:layout_height="3dp"
        android:progressTint="@color/nerva_primary"
        android:progressBackgroundTint="@color/nerva_dark_blue"
        android:visibility="gone"
        android:max="100" />

    <androidx.swiperefreshlayout.widget.SwipeRefreshLayout
        android:id="@+id/swipeRefresh"
        android:layout_width="match_parent"
        android:layout_height="match_parent">

        <WebView
            android:id="@+id/webView"
            android:layout_width="match_parent"
            android:layout_height="match_parent" />

    </androidx.swiperefreshlayout.widget.SwipeRefreshLayout>

</LinearLayout>
```

**Visual hierarchy:**
```
┌──────────────────────────────────────┐
│ 🔺 [3dp ProgressBar - #FF6B35]       │  ← visibility="gone" saat idle
│                                      │
│ ┌──────────────────────────────────┐ │
│ │                                  │ │
│ │   ╭──── SWIPE TO REFRESH ────╮  │ │
│ │   │                          │  │ │
│ │   │    WEB APP (FULL SCREEN) │  │ │
│ │   │    HTML + CSS + JS       │  │ │
│ │   │                          │  │ │
│ │   ╰──────────────────────────╯  │ │
│ └──────────────────────────────────┘ │
└──────────────────────────────────────┘
```

---

## 10. MAINACTIVITY.KT — Full Code

```kotlin
package com.nerva.app

import android.annotation.SuppressLint
import android.app.DownloadManager
import android.content.Context
import android.content.Intent
import android.graphics.Bitmap
import android.net.ConnectivityManager
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.View
import android.webkit.*
import android.widget.ProgressBar
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.appcompat.app.AppCompatDelegate
import androidx.core.view.ViewCompat
import androidx.core.view.WindowCompat
import androidx.core.view.WindowInsetsCompat
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout

class MainActivity : AppCompatActivity() {

    // View bindings
    private lateinit var webView: WebView
    private lateinit var swipeRefresh: SwipeRefreshLayout
    private lateinit var progressBar: ProgressBar

    // State
    private var lastBackPressTime: Long = 0
    private var fileUploadCallback: ValueCallback<Array<Uri>>? = null
    private var isPageLoaded = false
    private var isShowingErrorPage = false
    private var pendingDeepLink: String? = null

    // Loading timeout
    private val loadingTimeoutHandler = Handler(Looper.getMainLooper())
    private val loadingTimeoutRunnable = Runnable { showLoadingTimeout() }

    // File upload launcher (modern replacement for startActivityForResult)
    private val fileUploadLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result ->
        fileUploadCallback?.onReceiveValue(
            if (result.resultCode == RESULT_OK) {
                result.data?.data?.let { arrayOf(it) }
            } else null
        )
        fileUploadCallback = null
    }

    // URL from BuildConfig (set via build.gradle.kts)
    private val homeUrl: String get() = BuildConfig.HOME_URL
    private val appVersion: String get() = BuildConfig.APP_VERSION
    private val isDebug: Boolean get() = BuildConfig.IS_DEBUG

    // ──────────────────────────────────────────────────────────────
    // LIFECYCLE
    // ──────────────────────────────────────────────────────────────

    override fun onCreate(savedInstanceState: Bundle?) {
        // 🔄 Force dark theme (brand consistency)
        AppCompatDelegate.setDefaultNightMode(AppCompatDelegate.MODE_NIGHT_YES)

        setTheme(R.style.Theme.Nerva) // Remove splash theme
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        // 📐 Edge-to-edge: render behind system bars, handle insets manually
        WindowCompat.setDecorFitsSystemWindows(window, false)
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(android.R.id.content)) { v, insets ->
            val bottom = insets.getInsets(WindowInsetsCompat.Type.systemBars()).bottom
            val top = insets.getInsets(WindowInsetsCompat.Type.systemBars()).top
            v.setPadding(0, top, 0, bottom)
            insets
        }

        webView = findViewById(R.id.webView)
        swipeRefresh = findViewById(R.id.swipeRefresh)
        progressBar = findViewById(R.id.progressBar)

        // 🐛 Enable WebView remote debugging for debug builds
        if (isDebug) {
            WebView.setWebContentsDebuggingEnabled(true)
        }

        setupWebView()
        setupSwipeRefresh()
        setupConnectivityListener()
        setupBackHandler()

        handleDeepLink(intent)

        if (isNetworkAvailable()) {
            loadHomeUrl()
        } else {
            showNoConnectionDialog()
        }
    }

    override fun onNewIntent(intent: Intent) {
        super.onNewIntent(intent)
        handleDeepLink(intent)
    }

    override fun onSaveInstanceState(outState: Bundle) {
        super.onSaveInstanceState(outState)
        webView.saveState(outState)
    }

    override fun onRestoreInstanceState(savedInstanceState: Bundle) {
        super.onRestoreInstanceState(savedInstanceState)
        webView.restoreState(savedInstanceState)
    }

    override fun onDestroy() {
        loadingTimeoutHandler.removeCallbacks(loadingTimeoutRunnable)
        webView.destroy()
        super.onDestroy()
    }

    // ──────────────────────────────────────────────────────────────
    // DEEP LINK HANDLING
    // ──────────────────────────────────────────────────────────────

    private fun handleDeepLink(intent: Intent?) {
        val deepLink = intent?.data?.toString()
        if (deepLink != null && deepLink.startsWith(homeUrl)) {
            pendingDeepLink = deepLink
        }
    }

    // ──────────────────────────────────────────────────────────────
    // WEBVIEW SETUP
    // ──────────────────────────────────────────────────────────────

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebView() {
        // Reset error state
        isShowingErrorPage = false
        webView.apply {
            // ▸ JavaScript & DOM
            settings.javaScriptEnabled = true
            settings.domStorageEnabled = true
            settings.databaseEnabled = true

            // ▸ Zoom
            settings.builtInZoomControls = true
            settings.displayZoomControls = false
            settings.setSupportZoom(true)

            // ▸ Viewport
            settings.loadWithOverviewMode = true
            settings.useWideViewPort = true
            settings.layoutAlgorithm = WebSettings.LayoutAlgorithm.NARROW_COLUMNS

            // ▸ Cache
            settings.cacheMode = WebSettings.LOAD_DEFAULT

            // ▸ User Agent — identify as Nerva App
            val defaultUA = settings.userAgentString
            settings.userAgentString = "$defaultUA Nerva/$appVersion (Android ${Build.VERSION.RELEASE})"

            // ▸ Cookies
            CookieManager.getInstance().setAcceptCookie(true)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                CookieManager.getInstance().acceptThirdPartyCookies(this)
                settings.mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
            }

            // ▸ Background
            setBackgroundColor(android.graphics.Color.parseColor("#1A1A2E"))

            // ▸ WebViewClient (navigation, errors, SSL)
            webViewClient = object : WebViewClient() {

                override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                    super.onPageStarted(view, url, favicon)
                    progressBar.visibility = View.VISIBLE
                    progressBar.progress = 0
                    isPageLoaded = false
                    isShowingErrorPage = false
                    // ⏱ Start 30s loading timeout
                    loadingTimeoutHandler.removeCallbacks(loadingTimeoutRunnable)
                    loadingTimeoutHandler.postDelayed(loadingTimeoutRunnable, 30000L)
                }

                override fun onPageFinished(view: WebView?, url: String?) {
                    super.onPageFinished(view, url)
                    progressBar.visibility = View.GONE
                    swipeRefresh.isRefreshing = false
                    isPageLoaded = true
                    // ✅ Cancel loading timeout
                    loadingTimeoutHandler.removeCallbacks(loadingTimeoutRunnable)
                }

                override fun onReceivedError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    error: WebResourceError?
                ) {
                    if (request?.isForMainFrame == true) {
                        swipeRefresh.isRefreshing = false
                        loadingTimeoutHandler.removeCallbacks(loadingTimeoutRunnable)
                        isShowingErrorPage = true
                        when (error?.errorCode) {
                            ERROR_HOST_LOOKUP, ERROR_CONNECT, ERROR_TIMEOUT ->
                                showErrorPage(
                                    "Tidak dapat terhubung ke server.",
                                    "Periksa koneksi internet Anda dan pastikan server menyala."
                                )
                            ERROR_FILE_NOT_FOUND ->
                                showErrorPage(
                                    "Halaman tidak ditemukan.",
                                    "URL yang diminta tidak tersedia (404)."
                                )
                            else ->
                                showErrorPage(
                                    "Terjadi kesalahan.",
                                    "Kode: ${error?.errorCode}\n${error?.description}"
                                )
                        }
                    }
                }

                override fun onReceivedHttpError(
                    view: WebView?,
                    request: WebResourceRequest?,
                    errorResponse: WebResourceResponse?
                ) {
                    if (request?.isForMainFrame == true) {
                        loadingTimeoutHandler.removeCallbacks(loadingTimeoutRunnable)
                        val statusCode = errorResponse?.statusCode ?: 0
                        when {
                            statusCode == 500 -> showErrorPage("Server Error (500)", "Server mengalami gangguan. Silakan coba lagi.")
                            statusCode == 404 -> showErrorPage("Halaman Tidak Ditemukan (404)", "URL tidak tersedia di server.")
                        }
                    }
                }

                override fun onReceivedSslError(
                    view: WebView?,
                    handler: SslErrorHandler?,
                    error: SslError?
                ) {
                    // 🚨 PRODUCTION: change to handler.cancel()
                    handler?.proceed()
                }

                override fun shouldOverrideUrlLoading(
                    view: WebView?,
                    request: WebResourceRequest?
                ): Boolean {
                    val url = request?.url?.toString() ?: return false

                    return when {
                        url.endsWith(".apk") -> {
                            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                            true
                        }
                        url.startsWith("tel:") -> {
                            startActivity(Intent(Intent.ACTION_DIAL, Uri.parse(url)))
                            true
                        }
                        url.startsWith("mailto:") -> {
                            startActivity(Intent(Intent.ACTION_SENDTO, Uri.parse(url)))
                            true
                        }
                        url.startsWith("https://wa.me") || url.startsWith("whatsapp://") -> {
                            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                            true
                        }
                        url.startsWith("intent://") -> {
                            try {
                                startActivity(Intent.parseUri(url, Intent.URI_INTENT_SCHEME))
                            } catch (e: Exception) {
                                // fallback: open in WebView
                                false
                            }
                            true
                        }
                        !url.startsWith(homeUrl) && url.startsWith("http") -> {
                            startActivity(Intent(Intent.ACTION_VIEW, Uri.parse(url)))
                            true
                        }
                        else -> false // handle in WebView
                    }
                }
            }

            // ▸ WebChromeClient (progress, title, file upload)
            webChromeClient = object : WebChromeClient() {
                override fun onProgressChanged(view: WebView?, newProgress: Int) {
                    progressBar.progress = newProgress
                }

                override fun onReceivedTitle(view: WebView?, title: String?) {
                    // Optionally update activity title
                }

                override fun onShowFileChooser(
                    webView: WebView?,
                    filePathCallback: ValueCallback<Array<Uri>>?,
                    fileChooserParams: FileChooserParams?
                ): Boolean {
                    fileUploadCallback = filePathCallback
                    val intent = Intent(Intent.ACTION_GET_CONTENT).apply {
                        type = "*/*"
                        putExtra(Intent.EXTRA_MIME_TYPES, arrayOf("image/*", "application/pdf"))
                    }
                    fileUploadLauncher.launch(Intent.createChooser(intent, "Pilih File"))
                    return true
                }
            }

            // ▸ Download Listener
            setDownloadListener { url, userAgent, contentDisposition, mimeType, contentLength ->
                val request = DownloadManager.Request(Uri.parse(url)).apply {
                    setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                    setTitle("Nerva Download")
                    setDescription("Mengunduh file...")
                    addRequestHeader("User-Agent", userAgent ?: "")
                }
                val manager = getSystemService(DOWNLOAD_SERVICE) as DownloadManager
                manager.enqueue(request)
                Toast.makeText(this@MainActivity, "Download dimulai... Cek notifikasi.", Toast.LENGTH_SHORT).show()
            }

            // ▸ JavaScript Bridge
            addJavascriptInterface(NervaBridge(), "Android")
        }
    }

    // ──────────────────────────────────────────────────────────────
    // SWIPE TO REFRESH
    // ──────────────────────────────────────────────────────────────

    private fun setupSwipeRefresh() {
        swipeRefresh.apply {
            setColorSchemeResources(
                android.R.color.holo_orange_light,
                android.R.color.holo_blue_light,
                android.R.color.holo_green_light
            )
            setProgressBackgroundColorSchemeResource(android.R.color.white)
            setOnRefreshListener {
                if (isShowingErrorPage) {
                    // If on error page, reload the actual URL, not the error HTML
                    loadHomeUrl()
                } else {
                    webView.reload()
                }
            }
        }
    }

    // ──────────────────────────────────────────────────────────────
    // CONNECTIVITY MONITOR
    // ──────────────────────────────────────────────────────────────

    private fun setupConnectivityListener() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            val cm = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
            cm.registerDefaultNetworkCallback(object : ConnectivityManager.NetworkCallback() {
                override fun onAvailable(network: android.net.Network) {
                    runOnUiThread {
                        if (isShowingErrorPage) loadHomeUrl()
                        else if (!isPageLoaded) webView.reload()
                    }
                }
                override fun onLost(network: android.net.Network) {
                    runOnUiThread {
                        Toast.makeText(this@MainActivity, "Koneksi internet terputus.", Toast.LENGTH_SHORT).show()
                    }
                }
            })
        }
    }

    // ──────────────────────────────────────────────────────────────
    // BACK HANDLER — Modern OnBackPressedDispatcher
    // ──────────────────────────────────────────────────────────────

    private fun setupBackHandler() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack()
                } else {
                    val now = System.currentTimeMillis()
                    if (now - lastBackPressTime < 2000) {
                        finish()
                    } else {
                        lastBackPressTime = now
                        Toast.makeText(this@MainActivity, "Tekan sekali lagi untuk keluar", Toast.LENGTH_SHORT).show()
                    }
                }
            }
        })
    }

    // ──────────────────────────────────────────────────────────────
    // NETWORK CHECK
    // ──────────────────────────────────────────────────────────────

    private fun isNetworkAvailable(): Boolean {
        val cm = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            val network = cm.activeNetwork ?: return false
            val caps = cm.getNetworkCapabilities(network) ?: return false
            return caps.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
        }
        @Suppress("DEPRECATION")
        return cm.activeNetworkInfo?.isConnected == true
    }

    // ──────────────────────────────────────────────────────────────
    // LOAD URL
    // ──────────────────────────────────────────────────────────────

    private fun loadHomeUrl() {
        isShowingErrorPage = false
        // Prioritize deep link over default home URL
        val targetUrl = pendingDeepLink.also { pendingDeepLink = null } ?: homeUrl
        webView.loadUrl(targetUrl)
    }

    // ──────────────────────────────────────────────────────────────
    // LOADING TIMEOUT — Show error if page takes >30s
    // ──────────────────────────────────────────────────────────────

    private fun showLoadingTimeout() {
        if (!isPageLoaded) {
            isShowingErrorPage = true
            showErrorPage(
                "Waktu loading habis.",
                "Server tidak merespon dalam 30 detik. Periksa koneksi atau coba lagi."
            )
        }
    }

    // ──────────────────────────────────────────────────────────────
    // DIALOG: No Connection
    // ──────────────────────────────────────────────────────────────

    private fun showNoConnectionDialog() {
        progressBar.visibility = View.GONE
        AlertDialog.Builder(this)
            .setTitle("Tidak Ada Koneksi")
            .setMessage("Nerva memerlukan koneksi internet.\n\nPeriksa:\n• WiFi/data seluler aktif\n• Server menyala\n• URL benar")
            .setPositiveButton("Coba Lagi") { _, _ ->
                if (isNetworkAvailable()) loadHomeUrl() else showNoConnectionDialog()
            }
            .setNegativeButton("Tutup Aplikasi") { _, _ -> finish() }
            .setCancelable(false)
            .show()
    }

    // ──────────────────────────────────────────────────────────────
    // ERROR PAGE (custom HTML in WebView)
    // ──────────────────────────────────────────────────────────────

    private fun showErrorPage(title: String, message: String) {
        val errorHtml = """
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
                <style>
                    * { margin:0; padding:0; box-sizing:border-box; }
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        background: #1A1A2E; color: #FFFFFF;
                        display: flex; flex-direction: column; align-items: center; justify-content: center;
                        min-height: 100vh; padding: 32px 24px; text-align: center;
                    }
                    .icon { font-size: 72px; margin-bottom: 20px; }
                    h1 { font-size: 22px; color: #FF6B35; font-weight: 700; margin-bottom: 12px; }
                    p { font-size: 14px; color: #9E9E9E; line-height: 1.6; max-width: 320px; margin-bottom: 8px; }
                    .actions { margin-top: 24px; display: flex; flex-direction: column; gap: 10px; width: 100%; max-width: 260px; }
                    .btn-primary {
                        background: #FF6B35; color: #FFFFFF; border: none; padding: 14px 24px;
                        border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer;
                    }
                    .btn-secondary {
                        background: transparent; color: #9E9E9E; border: 1px solid #333;
                        padding: 10px 20px; border-radius: 10px; font-size: 14px; cursor: pointer;
                    }
                    .error-code { margin-top: 24px; font-size: 11px; color: #555; }
                </style>
            </head>
            <body>
                <div class="icon">📡</div>
                <h1>$title</h1>
                <p>${message.replace("\n", "<br>")}</p>
                <div class="actions">
                    <button class="btn-primary" onclick="window.NervaApp.reloadApp()">🔄 Coba Lagi</button>
                    <button class="btn-secondary" onclick="window.NervaApp.closeApp()">Tutup Aplikasi</button>
                </div>
                <div class="error-code">Nerva v$appVersion</div>
                <script>
                    window.NervaApp = {
                        closeApp: function() { Android.closeApp(); },
                        reloadApp: function() { Android.reloadApp(); }
                    };
                </script>
            </body>
            </html>
        """.trimIndent()
        webView.loadDataWithBaseURL(null, errorHtml, "text/html", "UTF-8", null)
    }

    // ──────────────────────────────────────────────────────────────
    // JAVASCRIPT BRIDGE
    // ──────────────────────────────────────────────────────────────

    private inner class NervaBridge {
        @JavascriptInterface
        fun showToast(message: String) {
            runOnUiThread { Toast.makeText(this@MainActivity, message, Toast.LENGTH_SHORT).show() }
        }

        @JavascriptInterface
        fun closeApp() {
            runOnUiThread { finish() }
        }

        @JavascriptInterface
        fun reloadApp() {
            runOnUiThread {
                isShowingErrorPage = false
                loadHomeUrl()
            }
        }

        @JavascriptInterface
        fun getAppVersion(): String = appVersion

        @JavascriptInterface
        fun shareText(text: String) {
            runOnUiThread {
                val intent = Intent(Intent.ACTION_SEND).apply {
                    type = "text/plain"
                    putExtra(Intent.EXTRA_TEXT, text)
                }
                startActivity(Intent.createChooser(intent, "Bagikan"))
            }
        }
    }
}
```

### 10.1 Execution Flow Diagram

```
onCreate()
  ├── setTheme(Theme.Nerva)          // remove splash
  ├── setContentView(activity_main)
  ├── findViewById() × 3
  ├── setupWebView()
  │   ├── WebSettings (JS, DOM, cache, UA, cookies)
  │   ├── WebViewClient (page, error, SSL, navigation)
  │   ├── WebChromeClient (progress, file upload)
  │   ├── DownloadListener
  │   └── JavaScriptInterface (NervaBridge)
  ├── setupSwipeRefresh()
  ├── setupConnectivityListener()
  ├── handleDeepLink(intent)
  ├── isNetworkAvailable()?
  │   ├── YES → loadHomeUrl()
  │   └── NO  → showNoConnectionDialog()
  └── END
```

---

## 11. WEBVIEW ENGINEERING

### 11.1 WebView Internal Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    WEBVIEW (Chromium)                        │
│                                                             │
│  ┌─────────────┐  ┌──────────────┐  ┌───────────────────┐   │
│  │ WebView     │  │ WebChrome    │  │ WebView           │   │
│  │ Client      │  │ Client       │  │ Settings           │   │
│  │ (navigasi,  │  │ (progress,   │  │ (JS, cache, UA,   │   │
│  │  error, SSL)│  │  file, title)│  │  zoom, viewport)  │   │
│  └──────┬──────┘  └──────┬───────┘  └────────┬──────────┘   │
│         │                │                    │              │
│         ▼                ▼                    ▼              │
│  ┌────────────────────────────────────────────────────────┐  │
│  │               RENDER ENGINE (Chromium)                  │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────────────────┐  │  │
│  │  │ Blink    │  │ V8       │  │ Layout & Paint       │  │  │
│  │  │ (HTML)   │  │ (JS)     │  │ Engine               │  │  │
│  │  └──────────┘  └──────────┘  └──────────────────────┘  │  │
│  │  ┌──────────────────────────────────────────────────┐   │  │
│  │  │         Compositor / GPU Rendering               │   │  │
│  │  └──────────────────────────────────────────────────┘   │  │
│  └────────────────────────────────────────────────────────┘  │
│                                                             │
│  Storage:                                                    │
│  ┌──────────┬──────────┬──────────┬──────────┬──────────┐   │
│  │ localStorage│Session  │ Cookies  │ Cache    │ Service  │   │
│  │            │Storage  │          │ (disk)   │ Worker   │   │
│  └──────────┴──────────┴──────────┴──────────┴──────────┘   │
└─────────────────────────────────────────────────────────────┘
```

### 11.2 Session & Cookie Persistence

```
User Login ──▶ Server → Set-Cookie header
                    │
                    ▼
           WebView: CookieManager menyimpan
                    │
                    ▼
           Request berikutnya: cookie otomatis dikirim
                    │
                    ▼
           Server verifikasi → session maintained
                    │
                    ▼
           App close → cookie persistent di disk
                    │
                    ▼
           App relaunch → cookie masih ada
```

**If session lost:**
1. User logout → server clears cookie
2. App data cleared (Settings → Apps → Nerva → Clear Data)
3. Cookie expired (server-side TTL)

### 11.3 Cache Strategy Comparison

| Strategy | Use Case | Nerva Default |
|----------|----------|---------------|
| `LOAD_DEFAULT` | Balance speed & freshness | ✅ Digunakan |
| `LOAD_CACHE_ELSE_NETWORK` | Prefer speed, stale OK | Emergency fallback |
| `LOAD_NO_CACHE` | Always fresh (kiosk mode) | — |
| `LOAD_CACHE_ONLY` | Offline mode | — |

### 11.4 JavaScript Bridge API Reference

**Registered as:** `Android` (via `addJavascriptInterface`)

| JS Call | Android Function | Description |
|---------|-----------------|-------------|
| `Android.showToast(msg)` | `NervaBridge.showToast()` | Show toast notification |
| `Android.closeApp()` | `NervaBridge.closeApp()` | Close the app |
| `Android.getAppVersion()` | `NervaBridge.getAppVersion()` | Get app version string |
| `Android.shareText(text)` | `NervaBridge.shareText()` | Share text via system share sheet |

**Usage in web app:**
```javascript
// Show toast
Android.showToast("Data berhasil disimpan!");

// Get version
const version = Android.getAppVersion();
console.log(`Nerva version: ${version}`);

// Share
Android.shareText("Check out this invoice: INV-001");

// Close app
Android.closeApp();
```

### 11.5 File Upload Pipeline

```
Web App: <input type="file"> →
  onCreateWebView → onShowFileChooser() →
    Intent.ACTION_GET_CONTENT →
      System file picker →
        onActivityResult(RESULT_OK, data) →
          fileUploadCallback.onReceiveValue(uri) →
            WebView menerima file →
              ✨ Upload sukses
```

---

## 12. RESOURCES & ASSETS

### 12.1 `res/values/colors.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<resources>
    <!-- Brand -->
    <color name="nerva_primary">#FF6B35</color>
    <color name="nerva_primary_dark">#E55A2B</color>
    <color name="nerva_primary_light">#FF8C5A</color>

    <!-- Background -->
    <color name="nerva_dark">#1A1A2E</color>
    <color name="nerva_dark_blue">#16213E</color>
    <color name="nerva_accent">#0F3460</color>

    <!-- Semantic -->
    <color name="nerva_success">#43A047</color>
    <color name="nerva_error">#E53935</color>
    <color name="nerva_warning">#FB8C00</color>
    <color name="nerva_info">#1E88E5</color>
    <color name="nerva_gold">#F59E0B</color>

    <!-- Neutral -->
    <color name="white">#FFFFFF</color>
    <color name="black">#000000</color>
    <color name="grey_100">#F5F5F5</color>
    <color name="grey_300">#E0E0E0</color>
    <color name="grey_500">#9E9E9E</color>
    <color name="grey_700">#616161</color>
    <color name="grey_900">#212121</color>

    <!-- Alpha -->
    <color name="transparent">#00000000</color>
    <color name="black_alpha_20">#33000000</color>
    <color name="black_alpha_50">#80000000</color>
</resources>
```

### 12.2 `res/values/strings.xml`

```xml
<resources>
    <string name="app_name">Nerva</string>
</resources>
```

### 12.3 `res/values/themes.xml`

```xml
<resources>
    <style name="Theme.Nerva" parent="Theme.AppCompat.DayNight.NoActionBar">
        <item name="colorPrimary">@color/nerva_primary</item>
        <item name="colorPrimaryDark">@color/nerva_primary_dark</item>
        <item name="colorAccent">@color/nerva_primary</item>
        <item name="android:statusBarColor">@color/nerva_dark</item>
        <item name="android:navigationBarColor">@color/nerva_dark</item>
        <item name="android:windowBackground">@color/nerva_dark</item>
        <item name="android:windowLightStatusBar">false</item>
    </style>

    <style name="Theme.Nerva.Splash" parent="Theme.Nerva">
        <item name="android:windowBackground">@drawable/splash_background</item>
    </style>
</resources>
```

### 12.4 `res/drawable/splash_background.xml`

```xml
<?xml version="1.0" encoding="utf-8"?>
<layer-list xmlns:android="http://schemas.android.com/apk/res/android">
    <item android:drawable="@color/nerva_dark" />
    <item
        android:drawable="@mipmap/ic_launcher"
        android:gravity="center" />
</layer-list>
```

---

## 13. GENERATE ICON & ASSETS

### 13.1 CLI-Based Icon Generation (No Android Studio)

**Option A: Android Asset Studio CLI (recommended)**

```bash
# Install Android Asset Studio CLI via Docker
docker run --rm -v $(pwd):/output \
  londonappdeveloper/android-asset-studio \
  --icon /path/to/your/logo.png \
  --output /output/app/src/main/res \
  --name ic_launcher

# Or use Python script
pip install android-asset-studio
android-asset-studio \
  --source logo.png \
  --output-dir app/src/main/res \
  --icon-name ic_launcher \
  --background-color "#FF6B35"
```

**Option B: Manual ImageMagick (no dependencies)**

```bash
# Generate all mipmap sizes from a single 1024x1024 PNG
# Prerequisite: sudo apt install imagemagick

LOGO="logo-1024.png"
OUT="app/src/main/res"

convert $LOGO -resize 48x48   $OUT/mipmap-mdpi/ic_launcher.png
convert $LOGO -resize 72x72   $OUT/mipmap-hdpi/ic_launcher.png
convert $LOGO -resize 96x96   $OUT/mipmap-xhdpi/ic_launcher.png
convert $LOGO -resize 144x144 $OUT/mipmap-xxhdpi/ic_launcher.png
convert $LOGO -resize 192x192 $OUT/mipmap-xxxhdpi/ic_launcher.png

# Round icons (same bitmap for simplicity; Android masks to circle)
cp $OUT/mipmap-mdpi/ic_launcher.png   $OUT/mipmap-mdpi/ic_launcher_round.png
cp $OUT/mipmap-hdpi/ic_launcher.png   $OUT/mipmap-hdpi/ic_launcher_round.png
cp $OUT/mipmap-xhdpi/ic_launcher.png  $OUT/mipmap-xhdpi/ic_launcher_round.png
cp $OUT/mipmap-xxhdpi/ic_launcher.png $OUT/mipmap-xxhdpi/ic_launcher_round.png
cp $OUT/mipmap-xxxhdpi/ic_launcher.png $OUT/mipmap-xxxhdpi/ic_launcher_round.png

echo "✅ Icons generated!"
```

### 13.2 Adaptive Icon (Android 8+)

Create `res/mipmap-anydpi-v26/ic_launcher.xml` and `res/mipmap-anydpi-v26/ic_launcher_round.xml`:

```xml
<!-- ic_launcher.xml -->
<?xml version="1.0" encoding="utf-8"?>
<adaptive-icon xmlns:android="http://schemas.android.com/apk/res/android">
    <background android:drawable="@color/nerva_primary"/>
    <foreground android:drawable="@drawable/ic_launcher_foreground"/>
</adaptive-icon>
```

```xml
<!-- ic_launcher_round.xml — identical content, separate file for manifest reference -->
<?xml version="1.0" encoding="utf-8"?>
<adaptive-icon xmlns:android="http://schemas.android.com/apk/res/android">
    <background android:drawable="@color/nerva_primary"/>
    <foreground android:drawable="@drawable/ic_launcher_foreground"/>
</adaptive-icon>
```

Create `res/drawable/ic_launcher_foreground.xml` (vector):

```xml
<?xml version="1.0" encoding="utf-8"?>
<vector xmlns:android="http://schemas.android.com/apk/res/android"
    android:width="108dp"
    android:height="108dp"
    android:viewportWidth="108"
    android:viewportHeight="108">
    <!-- Simple "N" shape as fallback -->
    <path
        android:fillColor="#FFFFFF"
        android:pathData="M36,36 L36,72 L48,72 L48,54 L60,72 L72,72 L72,36 L60,36 L60,54 L48,36 Z"/>
</vector>
```

---

## 14. BUILD CONFIG & FLAVORS

### 14.1 Build Variants

| Variant | applicationId | HOME_URL | Debuggable | Minify |
|---------|---------------|----------|------------|--------|
| **debug** | `com.nerva.app.debug` | `http://10.0.2.2/nerva` | ✅ Yes | ❌ No |
| **release** | `com.nerva.app` | `https://nerva.app` | ❌ No | ✅ Yes |

### 14.2 VSCode: Switch Build Variant

Use command palette or terminal:

```bash
# Debug
./gradlew assembleDebug

# Release
./gradlew assembleRelease

# Install debug to connected device
./gradlew installDebug
```

### 14.3 Environment-Specific URL Config

Use `buildConfigField` per build type in `app/build.gradle.kts`:

```kotlin
buildTypes {
    debug {
        buildConfigField("String", "HOME_URL", "\"http://10.0.2.2/nerva\"")
    }
    release {
        buildConfigField("String", "HOME_URL", "\"https://nerva.app\"")
    }
}
```

---

## 15. R8 / PROGUARD

### 15.1 `app/proguard-rules.pro`

```proguard
# ── WebView JavaScript Interface ──
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}

# ── BuildConfig ──
-keep class com.nerva.app.BuildConfig { *; }

# ── AndroidX ──
-keep class androidx.** { *; }
-keep interface androidx.** { *; }
-dontwarn androidx.**

# ── R8 Full Mode ──
-optimizationpasses 5
-allowaccessmodification
-mergeinterfacesaggressively

# ── Remove Log in Release ──
-assumenosideeffects class android.util.Log {
    public static boolean isLoggable(java.lang.String, int);
    public static *** d(...);
    public static *** v(...);
    public static *** i(...);
}
```

### 15.2 R8 Configuration in `build.gradle.kts`

R8 is the default shrinker in AGP 8+. No extra plugin needed.

```kotlin
release {
    isMinifyEnabled = true
    isShrinkResources = true
    proguardFiles(
        getDefaultProguardFile("proguard-android-optimize.txt"),
        "proguard-rules.pro"
    )
}
```

---

## 16. TESTING STRATEGY

### 16.1 Test Levels

| Level | Tool | Command | When |
|-------|------|---------|------|
| Unit Test | JUnit | `./gradlew test` | After code changes |
| Lint | Android Lint | `./gradlew lint` | Before commit |
| Manual Testing | Physical device | `./gradlew installDebug` | Before release |

### 16.2 Testing on Emulator (CLI)

```bash
# List available AVDs
$ANDROID_HOME/emulator/emulator -list-avds

# Start emulator headless
$ANDROID_HOME/emulator/emulator -avd Pixel_6_API_34 -no-window -no-audio &

# Wait for boot
adb wait-for-device

# Install & run
./gradlew installDebug
adb shell am start -n com.nerva.app.debug/.MainActivity
```

### 16.3 Testing on Physical Device

```bash
# Enable USB debugging on phone
# Settings → About Phone → Tap Build Number 7x
# Settings → Developer Options → USB Debugging

# Connect via USB
adb devices
# Should show: <device_id> device

# Install
./gradlew installDebug

# View logs
adb logcat -s Nerva:D
```

### 16.4 Chrome DevTools for WebView Debugging

> WebView remote debugging sudah otomatis aktif untuk **debug build** via `isDebug` di `MainActivity.onCreate()`. Tidak perlu kode manual.

```bash
# 1. Connect device via USB (USB debugging ON)
# 2. Install debug APK
./gradlew installDebug

# 3. Open Chrome on desktop → chrome://inspect
# 4. Click "inspect" on Nerva WebView
# 5. Full DevTools: Elements, Console, Network, Sources
```

### 16.5 Test Checklist (Validated by AI Agent)

```
□ Project compiles: ./gradlew assembleDebug → BUILD SUCCESSFUL
□ Lint passes: ./gradlew lint → no errors
□ Unit tests pass: ./gradlew test → all green
□ APK generated: app/build/outputs/apk/debug/*.apk exists
□ Splash screen shows (navy + logo)
□ WebView loads URL (progress bar orange)
□ Login page appears
□ Back button navigates history
□ Double-tap back exits app
□ Pull-to-refresh works
□ Screen rotation does NOT restart activity
□ Offline → error page shows
□ Online → retry works
□ External links open in browser
□ APK size < 8MB (debug), < 4MB (release)
```

---

## 17. BUILD APK (CLI)

### 17.1 Debug APK

```bash
# Clean build
./gradlew clean
./gradlew assembleDebug

# Output:
# app/build/outputs/apk/debug/app-debug.apk
```

### 17.2 Release APK (Signed)

```bash
# Prerequisite: keystore.properties + .jks file exist

# Build
./gradlew assembleRelease

# Output:
# app/build/outputs/apk/release/app-release.apk
```

### 17.3 Verify APK

```bash
# Check APK info
aapt dump badging app/build/outputs/apk/debug/app-debug.apk | head -10

# Or with Android SDK
$ANDROID_HOME/build-tools/34.0.0/aapt dump badging app/build/outputs/apk/debug/app-debug.apk

# Check signing
apksigner verify --print-certs app/build/outputs/apk/release/app-release.apk
```

### 17.4 APK Size Optimization Targets

| Variant | Target | Threshold (Alert) |
|---------|--------|-------------------|
| Debug | 4-6 MB | > 10 MB |
| Release (minified) | 2-4 MB | > 6 MB |

---

## 18. CODE SIGNING

### 18.1 Generate Keystore (CLI)

```bash
# Generate keystore (one-time)
keytool -genkey -v \
  -keystore nerva-keystore.jks \
  -alias nerva \
  -keyalg RSA \
  -keysize 2048 \
  -validity 9125 \
  -storepass <your-store-pass> \
  -keypass <your-key-pass> \
  -dname "CN=Nerva Developer, OU=Development, O=Nerva, L=Jakarta, ST=Indonesia, C=ID"

# Verify keystore
keytool -list -v -keystore nerva-keystore.jks -alias nerva -storepass <your-store-pass>
```

### 18.2 Keystore Security Best Practices

```
⚠️ KRITIKAL: Keystore + passwords adalah satu-satunya kunci
   untuk update aplikasi di Play Store.

Rules:
1. JANGAN commit .jks ke git
2. JANGAN commit keystore.properties ke git
3. Simpan backup keystore di password manager (1Password/Bitwarden)
4. Gunakan environment variable untuk CI/CD
5. Keystore password: minimal 16 karakter, kombinasi

Production (recommended):
  - Simpan keystore.properties di ~/.nerva/keystore.properties
  - Load di build.gradle.kts dari home directory
```

---

## 19. CI/CD PIPELINE

### 19.1 GitHub Actions Workflow

Create `.github/workflows/build.yml`:

```yaml
name: Nerva CI/CD

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-java@v4
        with:
          distribution: 'temurin'
          java-version: '17'
      - uses: actions/cache@v3
        with:
          path: |
            ~/.gradle/caches
            ~/.gradle/wrapper
          key: ${{ runner.os }}-gradle-${{ hashFiles('**/*.gradle*', '**/gradle-wrapper.properties') }}
      - run: ./gradlew lint

  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-java@v4
        with:
          distribution: 'temurin'
          java-version: '17'
      - run: ./gradlew test

  build-debug:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-java@v4
        with:
          distribution: 'temurin'
          java-version: '17'
      - uses: android-actions/setup-android@v3
      - run: ./gradlew assembleDebug
      - uses: actions/upload-artifact@v4
        with:
          name: nerva-debug
          path: app/build/outputs/apk/debug/*.apk

  build-release:
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-java@v4
        with:
          distribution: 'temurin'
          java-version: '17'
      - uses: android-actions/setup-android@v3
      - name: Decode Keystore
        env:
          KEYSTORE_BASE64: ${{ secrets.KEYSTORE_BASE64 }}
        run: |
          echo "$KEYSTORE_BASE64" | base64 --decode > nerva-keystore.jks
      - name: Create keystore.properties
        run: |
          cat > keystore.properties << EOF
          storeFile=nerva-keystore.jks
          storePassword=${{ secrets.KEYSTORE_PASSWORD }}
          keyAlias=${{ secrets.KEY_ALIAS }}
          keyPassword=${{ secrets.KEY_PASSWORD }}
          EOF
      - run: ./gradlew assembleRelease
      - uses: actions/upload-artifact@v4
        with:
          name: nerva-release
          path: app/build/outputs/apk/release/*.apk
```

### 19.2 GitHub Secrets Required

| Secret | Example Value | Purpose |
|--------|---------------|---------|
| `KEYSTORE_BASE64` | `base64 nerva-keystore.jks` | Keystore file encoded |
| `KEYSTORE_PASSWORD` | `P@ssw0rd!2345` | Keystore password |
| `KEY_ALIAS` | `nerva` | Key alias |
| `KEY_PASSWORD` | `K3yP@ss!789` | Key password |

---

## 20. DISTRIBUSI & UPDATE

### 20.1 Distribution Methods

| Method | Mechanism | Best For |
|--------|-----------|----------|
| **WhatsApp / Email** | Direct APK file | Internal testing (< 10 users) |
| **Google Drive** | Share link | Medium team (10-100 users) |
| **Firebase App Distribution** | SDK-less OTA | Enterprise testing |
| **Private Website** | Download page | All users |
| **Google Play Store** | Public/Private listing | Production public |
| **MDM (Intune, Jamf)** | Managed distribution | Enterprise managed devices |

### 20.2 Version Bump Automation

Create `scripts/bump-version.sh`:

```bash
#!/bin/bash
# Usage: ./bump-version.sh [major|minor|patch]

TYPE=${1:-patch}

# Read current version
VERSION=$(grep 'versionName' app/build.gradle.kts | sed 's/.*"\(.*\)".*/\1/')
IFS='.' read -r MAJOR MINOR PATCH <<< "$VERSION"

case $TYPE in
  major) MAJOR=$((MAJOR + 1)); MINOR=0; PATCH=0 ;;
  minor) MINOR=$((MINOR + 1)); PATCH=0 ;;
  patch) PATCH=$((PATCH + 1)) ;;
esac

NEW_VERSION="$MAJOR.$MINOR.$PATCH"
NEW_CODE=$((MAJOR * 10000 + MINOR * 100 + PATCH))

# Update build.gradle.kts
sed -i "s/versionCode = [0-9]*/versionCode = $NEW_CODE/" app/build.gradle.kts
sed -i "s/versionName = \"[^\"]*\"/versionName = \"$NEW_VERSION\"/" app/build.gradle.kts

echo "✅ Bumped to $NEW_VERSION (code: $NEW_CODE)"
```

---

## 21. FITUR ENTERPRISE

### 21.1 Push Notification (Firebase Cloud Messaging)

```kotlin
// 1. Add to app/build.gradle.kts:
// implementation(platform("com.google.firebase:firebase-bom:32.7.0"))
// implementation("com.google.firebase:firebase-messaging-ktx")

// 2. Create FirebaseService.kt
class NervaFirebaseService : FirebaseMessagingService() {
    override fun onMessageReceived(message: RemoteMessage) {
        // Show notification
        val notification = NotificationCompat.Builder(this, "nerva_channel")
            .setContentTitle(message.notification?.title)
            .setContentText(message.notification?.body)
            .setSmallIcon(R.drawable.ic_launcher_foreground)
            .setAutoCancel(true)
            .build()

        NotificationManagerCompat.from(this).notify(1001, notification)
    }

    override fun onNewToken(token: String) {
        // Send token to Nerva server
        // POST /api/device-token { token: "..." }
    }
}

// 3. Add to AndroidManifest.xml:
// <service android:name=".NervaFirebaseService" android:exported="false">
//     <intent-filter>
//         <action android:name="com.google.firebase.MESSAGING_EVENT" />
//     </intent-filter>
// </service>
```

### 21.2 Deep Linking

```xml
<!-- In AndroidManifest.xml inside <activity> -->
<intent-filter android:autoVerify="true">
    <action android:name="android.intent.action.VIEW" />
    <category android:name="android.intent.category.DEFAULT" />
    <category android:name="android.intent.category.BROWSABLE" />
    <data android:scheme="https" android:host="nerva.app" />
    <data android:scheme="http" android:host="nerva.app" />
</intent-filter>
```

### 21.3 Service Worker / Offline Support

Web app (Laravel/Vue) registers Service Worker. Android WebView supports SW natively from API 24 (Chrome engine).

```javascript
// In web app's main.js
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then(reg => console.log('SW registered', reg))
        .catch(err => console.error('SW failed', err));
}
```

### 21.4 App Version Check (Force Update)

```kotlin
// Inject this JS after page load to check version:
private fun checkAppVersion() {
    webView.evaluateJavascript("""
        (function() {
            fetch('/api/app-version')
                .then(r => r.json())
                .then(data => {
                    if (data.latestVersion !== '$appVersion') {
                        Android.showToast('Update tersedia: v' + data.latestVersion);
                    }
                });
        })();
    """.trimIndent(), null)
}

// Call in onPageFinished:
override fun onPageFinished(view: WebView?, url: String?) {
    // ...
    checkAppVersion()
}
```

### 21.5 Crash Reporting (Firebase Crashlytics)

```kotlin
// In app/build.gradle.kts:
// implementation(platform("com.google.firebase:firebase-bom:32.7.0"))
// implementation("com.google.firebase:firebase-crashlytics-ktx")

// In Application.onCreate() (or MainActivity):
// FirebaseCrashlytics.getInstance().setCrashlyticsCollectionEnabled(true)
```

### 21.6 Biometric Authentication

```kotlin
// Optional: lock app behind biometric
private fun authenticateWithBiometrics(callback: () -> Unit) {
    val biometricManager = BiometricManager.from(this)
    when (biometricManager.canAuthenticate(BiometricManager.Authenticators.BIOMETRIC_STRONG)) {
        BiometricManager.BIOMETRIC_SUCCESS -> {
            val prompt = BiometricPrompt(this, object : BiometricPrompt.AuthenticationCallback() {
                override fun onAuthenticationSucceeded(result: BiometricPrompt.AuthenticationResult) {
                    callback()
                }
            })
            prompt.authenticate(
                BiometricPrompt.PromptInfo.Builder()
                    .setTitle("Verifikasi Identitas")
                    .setSubtitle("Gunakan sidik jari atau wajah untuk membuka Nerva")
                    .setAllowedAuthenticators(BiometricManager.Authenticators.BIOMETRIC_STRONG)
                    .build()
            )
        }
        else -> callback() // Fallback: no biometric, proceed anyway
    }
}
```

---

## 22. TROUBLESHOOTING

### 22.1 Common Errors

| Error | Root Cause | Solution |
|-------|------------|----------|
| **WebView blank/white** | URL wrong, server down, no internet | Test URL in mobile browser first |
| **Cleartext HTTP not permitted** | Android 9+ blocks HTTP | Use HTTPS or `usesCleartextTraffic="true"` in manifest |
| **Session login lost** | Cookie not persisted | Check `CookieManager.getInstance().setAcceptCookie(true)` |
| **Web page not responsive** | Viewport not set | `useWideViewPort = true` and `loadWithOverviewMode = true` |
| **APK > 10 MB** | Minify disabled or too many deps | Enable `isMinifyEnabled = true`, check dependencies |
| **Build failed: SDK not found** | `local.properties` missing or wrong path | Check `sdk.dir` in `local.properties` |
| **INSTALL_FAILED_UPDATE_INCOMPATIBLE** | App already installed with different signature | Uninstall old app first |

### 22.2 Logcat Debugging (CLI)

```bash
# Filter by tag
adb logcat -s Nerva:D

# Filter by app PID
adb logcat --pid=$(adb shell pidof -s com.nerva.app)

# Save to file
adb logcat -d > crash-log.txt

# Clear log
adb logcat -c
```

### 22.3 Chrome DevTools Remote Debug

```bash
# Step-by-step:
# 1. Enable USB debugging on phone
# 2. Run: adb devices (verify device listed)
# 3. Open Chrome desktop → chrome://inspect
# 4. Click "inspect" on Nerva WebView
# 5. Use Console, Network, Elements tabs
```

### 22.4 Gradle Debug

```bash
# Build with stacktrace
./gradlew assembleDebug --stacktrace

# Build with scan (publishes to scans.gradle.com)
./gradlew assembleDebug --scan

# Build with info logging
./gradlew assembleDebug --info

# Clean gradle cache
./gradlew cleanBuildCache
rm -rf ~/.gradle/caches/
```

---

## 23. AI AGENT INSTRUCTIONS

### 23.1 Execution Protocol for AI

When executing this blueprint as an AI agent, follow this strict protocol:

```
PHASE 1: SETUP
  1. Install prerequisites (JDK 17, Android SDK CLI)
  2. Create project directory structure
  3. Init Gradle wrapper
  4. Create .vscode config files
  5. Verify SDK availability (java, adb, gradlew)

PHASE 2: CREATE FILES
  1. gradle/libs.versions.toml (version catalog)
  2. settings.gradle.kts
  3. build.gradle.kts (root + app)
  4. gradle.properties
  5. AndroidManifest.xml
  6. activity_main.xml (layout)
  7. themes.xml, colors.xml, strings.xml
  8. splash_background.xml
  9. network_security_config.xml
  10. MainActivity.kt (full code)
  11. proguard-rules.pro
  12. .gitignore

PHASE 3: VERIFY BUILD
  1. Run: ./gradlew assembleDebug
  2. Fix any compilation errors
  3. Run: ./gradlew lint
  4. Run: ./gradlew test

PHASE 4: GENERATE ARTIFACTS
  1. Generate icons (using ImageMagick or placeholder)
  2. Create keystore for signing (if release)
  3. Run: ./gradlew assembleRelease

PHASE 5: VALIDATION
  Verify against test checklist (Section 16.5)
```

### 23.2 Decision Tree for AI

```
Q: Does the project compile?
├── YES → Continue
└── NO  → Read error → Check:
          ├── SDK path in local.properties?
          ├── Gradle version compatible with AGP?
          ├── Missing dependencies in libs.versions.toml?
          └── Syntax errors in Kotlin/XML?

Q: Is this for development or production?
├── Dev  → Use debug variant, HTTP URLs
└── Prod → Use release variant, HTTPS, sign APK

Q: Need to change the target URL?
├── Change in app/build.gradle.kts → buildConfigField
└── Rebuild APK

Q: Session/cookies not persisting?
├── Check CookieManager enabled in setupWebView()
├── Check domStorageEnabled = true
└── Check server sets proper Set-Cookie headers
```

### 23.3 AI Validation Checkpoints

After each major file creation, the AI should validate:

```
✅ [CHECKPOINT 1] After file structure creation
    Expected: All directories exist
    Command: ls -R app/src/main/

✅ [CHECKPOINT 2] After build.gradle.kts creation
    Expected: Gradle sync succeeds
    Command: ./gradlew --version

✅ [CHECKPOINT 3] After all source files created
    Expected: Compilation succeeds
    Command: ./gradlew assembleDebug

✅ [CHECKPOINT 4] After APK generation
    Expected: APK file exists
    Command: ls -la app/build/outputs/apk/debug/
```

### 23.4 AI Configuration Reference

Key files the AI must read/write:

| File | AI Action |
|------|-----------|
| `gradle/libs.versions.toml` | WRITE: versions and dependencies |
| `app/build.gradle.kts` | WRITE: build config, URL, signing |
| `app/src/main/AndroidManifest.xml` | WRITE: permissions, activity, deep links |
| `app/src/main/java/com/nerva/app/MainActivity.kt` | WRITE: main application logic |
| `app/src/main/res/values/colors.xml` | WRITE: color tokens |
| `app/src/main/res/values/themes.xml` | WRITE: theme definitions |
| `app/src/main/res/layout/activity_main.xml` | WRITE: UI layout |
| `app/proguard-rules.pro` | WRITE: shrink/obfuscation rules |
| `.github/workflows/build.yml` | WRITE: CI/CD pipeline |
| `.vscode/tasks.json` | WRITE: build tasks |
| `keystore.properties` | WRITE (local only, NOT in VCS) |

---

## ✅ FINAL OUTPUT CHECKLIST

```
Before delivering APK, verify:

□ ├── Project Structure
□ │   ├── All directories exist
□ │   ├── .vscode/ configured
□ │   └── gradle wrapper initialized

□ ├── Build System
□ │   ├── libs.versions.toml complete
□ │   ├── build.gradle.kts (root) correct
□ │   ├── app/build.gradle.kts correct
□ │   ├── gradle.properties optimized
□ │   └── ./gradlew assembleDebug → SUCCESS

□ ├── Source Code
□ │   ├── AndroidManifest.xml (permissions, activity, deep link)
□ │   ├── MainActivity.kt (WebView, JS bridge, error handling)
□ │   ├── activity_main.xml (WebView + ProgressBar + SwipeRefresh)
□ │   ├── themes.xml + colors.xml + strings.xml
□ │   ├── network_security_config.xml
□ │   ├── splash_background.xml
□ │   └── proguard-rules.pro

□ ├── CI/CD
□ │   ├── GitHub Actions workflow
□ │   └── Artifact upload configured

□ ├── Security
□ │   ├── Keystore generated (release)
□ │   ├── keystore.properties in .gitignore
□ │   └── SSL handling set for production

□ └── APK
    ├── Debug APK: app/build/outputs/apk/debug/app-debug.apk
    ├── Release APK: app/build/outputs/apk/release/app-release.apk
    ├── APK size < 8 MB (debug), < 4 MB (release)
    └── Verified with aapt dump
```

---

**🎯 END OF BLUEPRINT — Nerva Android WebView App with VSCode**

Dokumen ini adalah panduan **end-to-end, enterprise-grade** untuk AI agent membangun aplikasi Android WebView menggunakan **VSCode + CLI toolchain**, tanpa Android Studio. Setiap bagian memiliki kode siap pakai, diagram, decision tree, dan validation checkpoint untuk memastikan hasil final berkualitas produksi.
