<?php
/**
 * Database - PDO Singleton bağlantı sınıfı
 * Tüm modeller bu sınıf üzerinden bağlantı alır.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                ]);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    die('Veritabanı bağlantı hatası: ' . $e->getMessage());
                } else {
                    die('Veritabanı bağlantısı kurulamadı. Lütfen daha sonra tekrar deneyin.');
                }
            }
        }
        return self::$instance;
    }

    /** Hazır sorgu çalıştır, sonucu dizi olarak döndür */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Tek satır döndür */
    public static function queryOne(string $sql, array $params = []): array|false
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /** INSERT/UPDATE/DELETE çalıştır, etkilenen satır sayısını döndür */
    public static function execute(string $sql, array $params = []): int
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Son eklenen satırın ID'sini döndür */
    public static function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
