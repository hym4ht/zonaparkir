/*
 * ================================================================
 *  SISTEM INFORMASI PARKIR — ESP32
 *  Versi: WiFi + OTA + HTTP POST ke CodeIgniter 3
 * ================================================================
 *  Library tambahan yang perlu diinstall di Arduino IDE:
 *
 *  (Sketch → Include Library → Manage Libraries → cari & install)
 *    ✅ "ESP32Servo"           by Kevin Harrington
 *    ✅ "LiquidCrystal I2C"   by Frank de Brabander
 *
 *  Library berikut SUDAH BUILT-IN di ESP32 Arduino Core
 *  (tidak perlu install manual):
 *    ✅ WiFi.h
 *    ✅ ArduinoOTA.h
 *    ✅ HTTPClient.h
 *
 *  Pastikan board ESP32 sudah terinstall:
 *  File → Preferences → Additional Boards Manager URLs:
 *  https://raw.githubusercontent.com/espressif/arduino-esp32/gh-pages/package_esp32_index.json
 *  Lalu: Tools → Board → Boards Manager → cari "esp32" → Install
 * ================================================================
 */

// ================= LIBRARY TAMBAHAN (BARU) =================
#include <WiFi.h>
#include <ArduinoOTA.h>
#include <HTTPClient.h>

// ================= LIBRARY LAMA (SAMA) =================
#include <ESP32Servo.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// ================================================================
//  ⚙️  KONFIGURASI WIFI & SERVER — SESUAIKAN INI!
// ================================================================

// Ganti dengan nama & password WiFi Anda
const char* WIFI_SSID = "NAMA_WIFI_ANDA";
const char* WIFI_PASS = "PASSWORD_WIFI_ANDA";

// IP komputer Laragon Anda (jalankan "ipconfig" di CMD, lihat IPv4)
// Contoh: "192.168.1.100"
const char* SERVER_IP = "192.168.1.100";

// API Key — harus sama dengan di file Api.php
const char* API_KEY   = "parkir_secret_2025";

// Nama ESP32 ini di jaringan (muncul di Arduino IDE saat OTA)
const char* OTA_HOSTNAME = "ESP32-Parkir";
const char* OTA_PASSWORD = "parkir123";

// ================= LCD =================
LiquidCrystal_I2C lcd(0x27, 16, 2);

// ================= SERVO =================
Servo palang;

// ================= PIN (SAMA) =================
#define IR_MASUK  23
#define IR_KELUAR 15
#define IR_SLOT1  19
#define IR_SLOT2  18
#define IR_SLOT3  5
#define IR_SLOT4  27

#define BUZZER    4
#define SERVO_PIN 13

#define LED_MERAH  12
#define LED_HIJAU  14

// ================= VARIABEL LAMA (SAMA) =================
const int totalSlot = 4;
bool gateOpen = false;
unsigned long openStart   = 0;
const unsigned long openDuration = 5000;

// ================= VARIABEL BARU (TAMBAHAN) =================
bool wifiConnected        = false;
unsigned long lastKirim   = 0;
const unsigned long INTERVAL_KIRIM = 1500; // kirim ke server tiap 1.5 detik

// ================================================================
void setup() {
  Serial.begin(115200);
  Serial.println("\n===== SISTEM PARKIR ESP32 =====");

  // ================= PIN MODE (SAMA) =================
  pinMode(IR_MASUK,  INPUT);
  pinMode(IR_KELUAR, INPUT);
  pinMode(IR_SLOT1,  INPUT);
  pinMode(IR_SLOT2,  INPUT);
  pinMode(IR_SLOT3,  INPUT);
  pinMode(IR_SLOT4,  INPUT);

  pinMode(BUZZER,    OUTPUT);
  pinMode(LED_MERAH, OUTPUT);
  pinMode(LED_HIJAU, OUTPUT);

  digitalWrite(BUZZER,    LOW);
  digitalWrite(LED_MERAH, HIGH);
  digitalWrite(LED_HIJAU, LOW);

  // ================= SERVO (SAMA) =================
  palang.setPeriodHertz(50);
  palang.attach(SERVO_PIN, 500, 2400);
  palang.write(0);

  // ================= LCD (SAMA) =================
  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();

  lcd.setCursor(0, 0);
  lcd.print("SISTEM PARKIR");
  lcd.setCursor(0, 1);
  lcd.print("SEDANG SIAP...");
  delay(2000);
  lcd.clear();

  // ================= WIFI (BARU) =================
  lcd.setCursor(0, 0);
  lcd.print("Konek WiFi...");
  lcd.setCursor(0, 1);
  lcd.print(WIFI_SSID);

  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASS);

  Serial.print("Menghubungkan ke WiFi");
  int coba = 0;
  while (WiFi.status() != WL_CONNECTED && coba < 30) {
    delay(500);
    Serial.print(".");
    coba++;
  }

  if (WiFi.status() == WL_CONNECTED) {
    wifiConnected = true;
    Serial.println("\nWiFi OK! IP: " + WiFi.localIP().toString());
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi Terhubung!");
    lcd.setCursor(0, 1);
    lcd.print(WiFi.localIP().toString());
    delay(2000);
  } else {
    Serial.println("\nWiFi GAGAL. Lanjut offline.");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("WiFi GAGAL!");
    lcd.setCursor(0, 1);
    lcd.print("Mode Offline");
    delay(2000);
  }

  // ================= OTA (BARU) =================
  // OTA = upload firmware baru via WiFi tanpa kabel USB
  ArduinoOTA.setHostname(OTA_HOSTNAME);
  ArduinoOTA.setPassword(OTA_PASSWORD);

  ArduinoOTA.onStart([]() {
    Serial.println("OTA: Mulai upload firmware...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("OTA UPDATE...");
    lcd.setCursor(0, 1);
    lcd.print("Jangan Cabut!");
  });

  ArduinoOTA.onEnd([]() {
    Serial.println("OTA: Selesai! Restart...");
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("OTA Selesai!");
    lcd.setCursor(0, 1);
    lcd.print("Restarting...");
  });

  ArduinoOTA.onProgress([](unsigned int progress, unsigned int total) {
    int pct = progress / (total / 100);
    Serial.printf("OTA Progress: %u%%\r", pct);
    lcd.setCursor(0, 1);
    lcd.print("Progress: " + String(pct) + "%   ");
  });

  ArduinoOTA.onError([](ota_error_t error) {
    Serial.printf("OTA Error[%u]\n", error);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print("OTA ERROR!");
  });

  ArduinoOTA.begin();
  Serial.println("OTA siap. Port: " + String(OTA_HOSTNAME));

  lcd.clear();
}

// ================================================================
void loop() {

  // ================= OTA HANDLE (BARU — WAJIB DI BARIS PERTAMA) =================
  // Baris ini yang membuat OTA bekerja. Tanpa ini, OTA tidak aktif.
  ArduinoOTA.handle();

  // ================= CEK WIFI RECONNECT (BARU) =================
  if (WiFi.status() != WL_CONNECTED) {
    wifiConnected = false;
    WiFi.reconnect();
    delay(1000);
  } else {
    wifiConnected = true;
  }

  // ===== SENSOR (SAMA) =====
  bool sensorMasuk  = digitalRead(IR_MASUK)  == LOW;
  bool sensorKeluar = digitalRead(IR_KELUAR) == LOW;

  bool slot1 = digitalRead(IR_SLOT1) == LOW;
  bool slot2 = digitalRead(IR_SLOT2) == LOW;
  bool slot3 = digitalRead(IR_SLOT3) == LOW;
  bool slot4 = digitalRead(IR_SLOT4) == LOW;

  // ===== HITUNG TERISI (SAMA) =====
  int terisi = 0;
  if (slot1) terisi++;
  if (slot2) terisi++;
  if (slot3) terisi++;
  if (slot4) terisi++;

  // ===== SLOT KOSONG A,B,C,D (SAMA) =====
  String kosong = "";

  if (!slot1) kosong += "A,";
  if (!slot2) kosong += "B,";
  if (!slot3) kosong += "C,";
  if (!slot4) kosong += "D,";

  if (kosong.length() > 0) {
    kosong.remove(kosong.length() - 1);
  }

  // ===== LCD (SAMA + tambah indikator WiFi di kanan) =====
  lcd.setCursor(0, 0);
  lcd.print("Slot: ");
  lcd.print(terisi);
  lcd.print("/");
  lcd.print(totalSlot);

  // Tampilkan status WiFi di pojok kanan baris 1
  lcd.setCursor(10, 0);
  lcd.print(wifiConnected ? " [WiFi]" : " [NoNt]");

  lcd.setCursor(0, 1);
  if (kosong == "") {
    lcd.print("PENUH           ");
  } else {
    lcd.print("Kosong: ");
    lcd.print(kosong);
    lcd.print("   ");
  }

  // ===== MASUK (SAMA) =====
  if (sensorMasuk && !gateOpen) {
    if (terisi < totalSlot) {
      bukaPalang("masuk"); // ← sekarang kirim jenis ke server
    } else {
      digitalWrite(BUZZER, HIGH);
      delay(1500);
      digitalWrite(BUZZER, LOW);
    }
  }

  // ===== KELUAR (SAMA) =====
  if (sensorKeluar && !gateOpen) {
    bukaPalang("keluar"); // ← sekarang kirim jenis ke server
  }

  // ===== AUTO TUTUP (SAMA) =====
  if (gateOpen && millis() - openStart > openDuration) {
    tutupPalang();
  }

  // ===== KIRIM DATA SLOT KE SERVER (BARU) =====
  // Setiap 1.5 detik, kirim status slot ke web server
  if (millis() - lastKirim >= INTERVAL_KIRIM) {
    lastKirim = millis();
    kirimDataSlot(slot1, slot2, slot3, slot4);
  }

  delay(200);
}

// ================================================================
//  FUNGSI LAMA — bukaPalang & tutupPalang
//  Diubah: bukaPalang sekarang punya parameter "jenis"
//  untuk membedakan masuk/keluar dan kirim log ke server
// ================================================================
void bukaPalang(String jenis) {
  palang.write(90);
  gateOpen  = true;
  openStart = millis();

  digitalWrite(LED_MERAH, LOW);
  digitalWrite(LED_HIJAU, HIGH);

  Serial.println("Gate BUKA: " + jenis);

  // Kirim log ke server (masuk / keluar)
  kirimLogGate(jenis);
}

void tutupPalang() {
  palang.write(0);
  gateOpen = false;

  digitalWrite(LED_MERAH, HIGH);
  digitalWrite(LED_HIJAU, LOW);

  Serial.println("Gate TUTUP");
}

// ================================================================
//  FUNGSI BARU — Kirim Status Slot ke Server (setiap 1.5 detik)
// ================================================================
void kirimDataSlot(bool s1, bool s2, bool s3, bool s4) {
  if (!wifiConnected) {
    Serial.println("Skip kirim: WiFi tidak konek");
    return;
  }

  String url = "http://" + String(SERVER_IP) + "/infoparkir/api/update_slot";

  // Data JSON: 1 = terisi, 0 = kosong
  String body = "{";
  body += "\"slot1\":" + String(s1 ? 1 : 0) + ",";
  body += "\"slot2\":" + String(s2 ? 1 : 0) + ",";
  body += "\"slot3\":" + String(s3 ? 1 : 0) + ",";
  body += "\"slot4\":" + String(s4 ? 1 : 0);
  body += "}";

  HTTPClient http;
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", API_KEY);
  http.setTimeout(3000);

  int kode = http.POST(body);

  if (kode == 200) {
    Serial.println("Slot terkirim OK → " + body);
  } else {
    Serial.println("Gagal kirim slot. HTTP: " + String(kode));
  }

  http.end();
}

// ================================================================
//  FUNGSI BARU — Kirim Log Gate ke Server (saat palang dibuka)
// ================================================================
void kirimLogGate(String jenis) {
  if (!wifiConnected) return;

  String url  = "http://" + String(SERVER_IP) + "/infoparkir/api/gate_log";
  String body = "{\"jenis\":\"" + jenis + "\"}";

  HTTPClient http;
  http.begin(url);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-API-Key", API_KEY);
  http.setTimeout(3000);

  int kode = http.POST(body);

  if (kode == 200) {
    Serial.println("Log gate (" + jenis + ") terkirim OK");
  } else {
    Serial.println("Gagal kirim log. HTTP: " + String(kode));
  }

  http.end();
}
