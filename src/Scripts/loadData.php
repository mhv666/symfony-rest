#! /usr/local/bin/php
<?php
require_once '../../vendor/autoload.php';

$faker = Faker\Factory::create();
$faker->addProvider(new Faker\Provider\Lorem($faker));
$faker->addProvider(new Faker\Provider\Barcode($faker));
$faker->addProvider(new Faker\Provider\Color($faker));

$symfonyDDBB = 'symfony-api-database';

$isTest = $argv[1];

if (!is_null($isTest)) {
    $symfonyDDBB .= '_test';
}


$dsn = "mysql:host=symfony-db;dbname={$symfonyDDBB}";
try {
    $mbd =  new PDO($dsn, 'user', 'password');
    $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mbd->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $sql = '';


    //delete all tables 
    $stmt = $mbd->prepare('DELETE from products;');
    $stmt->execute();


    $stmt = $mbd->prepare('DELETE from merchants;');
    $stmt->execute();

    $stmt = $mbd->prepare('DELETE from countries;');
    $stmt->execute();

    $stmt = $mbd->prepare('DELETE from categories;');
    $stmt->execute();



    //End delete

    //categories
    $categories = ["vegetables", "fruits", "meat", "fish"];
    for ($i = 0; $i < count($categories); $i++) {
        $stmt = $mbd->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bindParam(1, $categories[$i]);
        $stmt->execute();
    }
    //countries
    for ($i = 0; $i < 10; $i++) {
        $stmt = $mbd->prepare("INSERT INTO countries (name) VALUES (?)");
        $stmt->bindParam(1, $faker->country());
        $stmt->execute();
    }

    //merchants
    foreach ($mbd->query('SELECT * from countries') as $row) {

        if (isset($row['id'])) {
            $now = new DateTimeImmutable('now');
            $stmt = $mbd->prepare("INSERT INTO merchants (country_id, merchant_name, created_at) VALUES (?,?,?)");
            $stmt->bindParam(1, $row['id']);
            $stmt->bindParam(2, $faker->name);
            $stmt->bindParam(3, $now->format('Y-m-d H:i:s'));

            $stmt->execute();
        }
    }
    //row categories
    $stmt = $mbd->prepare('SELECT id from categories');
    $stmt->execute();
    $rows_categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    //row merchants
    $stmt = $mbd->prepare('SELECT id from merchants');
    $stmt->execute();
    $rows_merchants = $stmt->fetchAll(\PDO::FETCH_COLUMN);

    for ($i = 0; $i < 40; $i++) {
        $now = new DateTimeImmutable('now');
        $stmt = $mbd->prepare("INSERT INTO products (name, image, color,merchant_id,category_id,price,description,ean13,stock,tax_percentage,created_at)
                                VALUES (:name,:image,:color,:merchant_id,:category_id,:price,:description,:ean13,:stock,:tax_percentage,:created_at)");
        $stmt->bindParam(':name', $faker->word());
        $stmt->bindParam(':image', $faker->imageUrl(640, 480, 'food'));
        $stmt->bindParam(':color', $faker->safeColorName());
        $stmt->bindParam(':merchant_id', $rows_merchants[rand(0, 9)]);
        $stmt->bindParam(':category_id', $rows_categories[rand(0, 3)]);
        $stmt->bindParam(':price', $faker->randomFloat(2, 1.1, 1000));
        $stmt->bindParam(':description', $faker->sentence(30));
        $stmt->bindParam(':ean13', $faker->ean13());
        $stmt->bindParam(':stock', $faker->randomDigit());
        $stmt->bindParam(':tax_percentage', $faker->numberBetween(1, 21));
        $stmt->bindParam(':created_at', $now->format('Y-m-d H:i:s'));
        $stmt->execute();
    }
    $mbd = null;
} catch (PDOException $e) {
    print "¡Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>