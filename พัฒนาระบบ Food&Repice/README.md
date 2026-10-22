# NOIRÉ Product Studio — PHP OOP CRUD

เว็บตัวอย่างจัดการสินค้า PHP OOP + MySQL สำหรับ XAMPP ธีมดำ-ครีม Responsive

## ฟังก์ชัน
- เพิ่ม / ดู / แก้ไข / ลบสินค้า (CRUD)
- ค้นหาตามชื่อและหมวดหมู่
- สรุปจำนวนรายการและมูลค่าสินค้าคงคลัง
- แจ้งสถานะสินค้า พร้อมขาย / ใกล้หมด / หมดสินค้า
- ใช้ PDO prepared statements, output escaping และ CSRF token
- แยกไฟล์ PHP, class, CSS และ SQL

## ติดตั้งบน XAMPP (Windows)
1. แตก ZIP แล้วนำโฟลเดอร์ `sk031_black_cream` ไปไว้ใน `C:\\xampp\\htdocs\\`
2. เปิด XAMPP Control Panel แล้ว Start `Apache` และ `MySQL`
3. เปิด `http://localhost/phpmyadmin`
4. ไปที่แท็บ **Import** เลือกไฟล์ `database.sql` แล้วกด **Import** หรือเปิดแท็บ SQL แล้ววางเนื้อหาไฟล์นี้เพื่อรัน
5. ตรวจสอบการเชื่อมต่อใน `app/Database.php` ค่าเริ่มต้นคือ host `localhost`, database `noire_inventory`, user `root`, password ว่าง (ค่าเริ่มต้น XAMPP)
6. เปิด `http://localhost/sk031_black_cream/`

## โครงสร้าง
```text
sk031_black_cream/
├── index.php
├── database.sql
├── README.md
├── app/
│   ├── Database.php
│   ├── Product.php
│   └── helpers.php
└── assets/
    └── style.css
```

## หมายเหตุ
- ตัวอย่างนี้เป็นโปรเจกต์ใหม่ที่สร้างตามโจทย์ PHP OOP CRUD ไม่ใช่สำเนาไฟล์ภายใน repository ต้นฉบับ
- ถ้าตั้งรหัสผ่าน MySQL ไว้ ให้แก้ `$password` ใน `app/Database.php`
- ถ้าพบว่า `mb_substr()` ไม่มี ให้เปิดใช้งาน PHP extension `mbstring` ใน `php.ini` แล้ว restart Apache
