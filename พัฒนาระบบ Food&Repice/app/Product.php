<?php
require_once __DIR__ . '/Database.php';

class Product {
    private PDO $db;
    public function __construct() { $this->db = (new Database())->connect(); }

    public function all(string $search = ''): array {
        if ($search !== '') {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE name LIKE :q OR category LIKE :q ORDER BY created_at DESC, id DESC');
            $stmt->execute(['q' => '%' . $search . '%']);
            return $stmt->fetchAll();
        }
        return $this->db->query('SELECT * FROM products ORDER BY created_at DESC, id DESC')->fetchAll();
    }
    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function create(array $data): void {
        $stmt = $this->db->prepare('INSERT INTO products (name, category, price, stock, description) VALUES (:name, :category, :price, :stock, :description)');
        $stmt->execute($data);
    }
    public function update(int $id, array $data): void {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE products SET name=:name, category=:category, price=:price, stock=:stock, description=:description WHERE id=:id');
        $stmt->execute($data);
    }
    public function delete(int $id): void {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }
    public function count(): int {
        return (int)$this->db->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }
    public function stockValue(): float {
        return (float)$this->db->query('SELECT COALESCE(SUM(price * stock), 0) FROM products')->fetchColumn();
    }
}
