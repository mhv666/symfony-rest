
## docker installation

```bash
make run
```

## List

```
http://localhost:300/api/products
```

## get

```
http://localhost:300/api/products/80
```

## post

```
POST URL http://localhost:300/api/products

{
"name": "placeat",
"description": "Qui optio consectetur ad ullam perspiciatis velit mollitia consequatur reprehenderit soluta quisquam qui numquam temporibus aperiam harum sed nesciunt quia sunt et et aut et.",
"image": "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
"color": "teal",
"merchant": 228,
"category": 130,
"price": 290.28,
"ean13": "6938832514614",
"stock": 3,
"tax_percentage": 2
}
```

## delete

```
http://localhost:300/api/products/81
```

## PUT

```
PUT http://localhost:300/api/products/81

{
"name": "eteee",
"description": "Qui optio consectetur ad ullam perspiciatis velit mollitia consequatur reprehenderit soluta quisquam qui numquam temporibus aperiam harum sed nesciunt quia sunt et et aut et.",
"image": "/tmp/d63fde10266c48b744b78e2f2762ef71.png",
"color": "teal",
"merchant": 228,
"category": 130,
"price": 290.28,
"ean13": "6938832514614",
"stock": 3,
"tax_percentage": 2
}
```
