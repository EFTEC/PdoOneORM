<?php http_response_code(404); die(1); // eftec/CliOne(1.24) configuration file (date gen: 2023-03-11 18:13)?>
{
    "conversion": {
        "bigint": null,
        "blob": null,
        "char": null,
        "date": null,
        "datetime": null,
        "decimal": null,
        "double": null,
        "enum": null,
        "float": null,
        "geometry": null,
        "int": null,
        "json": null,
        "longblob": null,
        "mediumint": null,
        "mediumtext": null,
        "set": null,
        "smallint": null,
        "text": null,
        "time": null,
        "timestamp": null,
        "tinyint": null,
        "varbinary": null,
        "varchar": null,
        "year": null
    },
    "alias": [],
    "columnsAlias": {
        "actor": {
            "actor_id": "actor_id",
            "first_name": "first_name",
            "last_name": "last_name",
            "last_update": "last_update",
            "_film_actor": "_film_actor"
        },
        "address": {
            "address_id": "address_id",
            "address": "address",
            "address2": "address2",
            "district": "district",
            "city_id": "city_id",
            "postal_code": "postal_code",
            "phone": "phone",
            "last_update": "last_update",
            "_city_id": "_city_id",
            "_customer": "_customer",
            "_staff": "_staff",
            "_store": "_store"
        },
        "category": {
            "category_id": "category_id",
            "name": "name",
            "last_update": "last_update",
            "_film_category": "_film_category"
        },
        "city": {
            "city_id": "city_id",
            "city": "city",
            "country_id": "country_id",
            "last_update": "last_update",
            "_country_id": "_country_id",
            "_address": "_address"
        },
        "country": {
            "country_id": "country_id",
            "country": "country",
            "last_update": "last_update",
            "_city": "_city"
        },
        "customer": {
            "customer_id": "customer_id",
            "store_id": "store_id",
            "first_name": "first_name",
            "last_name": "last_name",
            "email": "email",
            "address_id": "address_id",
            "active": "active",
            "create_date": "create_date",
            "last_update": "last_update",
            "_address_id": "_address_id",
            "_store_id": "_store_id",
            "_payment": "_payment",
            "_rental": "_rental"
        },
        "film": {
            "film_id": "film_id",
            "title": "title",
            "description": "description",
            "release_year": "release_year",
            "language_id": "language_id",
            "original_language_id": "original_language_id",
            "rental_duration": "rental_duration",
            "rental_rate": "rental_rate",
            "length": "length",
            "replacement_cost": "replacement_cost",
            "rating": "rating",
            "special_features": "special_features",
            "last_update": "last_update",
            "_language_id": "_language_id",
            "_original_language_id": "_original_language_id",
            "_film_actor": "_film_actor",
            "_inventory": "_inventory"
        },
        "film_actor": {
            "actor_id": "actor_id",
            "film_id": "film_id",
            "last_update": "last_update",
            "_actor_id": "_actor_id",
            "_film_id": "_film_id"
        },
        "film_category": {
            "film_id": "film_id",
            "category_id": "category_id",
            "last_update": "last_update",
            "_category_id": "_category_id"
        },
        "film_text": {
            "film_id": "film_id",
            "title": "title",
            "description": "description"
        },
        "inventory": {
            "inventory_id": "inventory_id",
            "film_id": "film_id",
            "store_id": "store_id",
            "last_update": "last_update",
            "_film_id": "_film_id",
            "_store_id": "_store_id",
            "_rental": "_rental"
        },
        "language": {
            "language_id": "language_id",
            "name": "name",
            "last_update": "last_update",
            "_film": "_film"
        },
        "payment": {
            "payment_id": "payment_id",
            "customer_id": "customer_id",
            "staff_id": "staff_id",
            "rental_id": "rental_id",
            "amount": "amount",
            "payment_date": "payment_date",
            "last_update": "last_update",
            "_customer_id": "_customer_id",
            "_rental_id": "_rental_id",
            "_staff_id": "_staff_id"
        },
        "rental": {
            "rental_id": "rental_id",
            "rental_date": "rental_date",
            "inventory_id": "inventory_id",
            "customer_id": "customer_id",
            "return_date": "return_date",
            "staff_id": "staff_id",
            "last_update": "last_update",
            "_customer_id": "_customer_id",
            "_inventory_id": "_inventory_id",
            "_staff_id": "_staff_id",
            "_payment": "_payment"
        },
        "staff": {
            "staff_id": "staff_id",
            "first_name": "first_name",
            "last_name": "last_name",
            "address_id": "address_id",
            "picture": "picture",
            "email": "email",
            "store_id": "store_id",
            "active": "active",
            "username": "username",
            "password": "password",
            "last_update": "last_update",
            "_address_id": "_address_id",
            "_store_id": "_store_id",
            "_payment": "_payment",
            "_rental": "_rental",
            "_store": "_store"
        },
        "store": {
            "store_id": "store_id",
            "manager_staff_id": "manager_staff_id",
            "address_id": "address_id",
            "last_update": "last_update",
            "_address_id": "_address_id",
            "_manager_staff_id": "_manager_staff_id",
            "_customer": "_customer",
            "_inventory": "_inventory",
            "_staff": "_staff"
        }
    },
    "columnsTable": {
        "actor": {
            "actor_id": null,
            "first_name": null,
            "last_name": null,
            "last_update": null,
            "_film_actor": "ONETOMANY"
        },
        "address": {
            "address_id": null,
            "address": null,
            "address2": null,
            "district": null,
            "city_id": null,
            "postal_code": null,
            "phone": null,
            "last_update": null,
            "_city_id": "MANYTOONE",
            "_customer": "ONETOMANY",
            "_staff": "ONETOMANY",
            "_store": "ONETOMANY"
        },
        "category": {
            "category_id": null,
            "name": null,
            "last_update": null,
            "_film_category": "ONETOMANY"
        },
        "city": {
            "city_id": null,
            "city": null,
            "country_id": null,
            "last_update": null,
            "_country_id": "MANYTOONE",
            "_address": "ONETOMANY"
        },
        "country": {
            "country_id": null,
            "country": null,
            "last_update": null,
            "_city": "ONETOMANY"
        },
        "customer": {
            "customer_id": null,
            "store_id": null,
            "first_name": null,
            "last_name": null,
            "email": null,
            "address_id": null,
            "active": null,
            "create_date": null,
            "last_update": null,
            "_address_id": "MANYTOONE",
            "_store_id": "MANYTOONE",
            "_payment": "ONETOMANY",
            "_rental": "ONETOMANY"
        },
        "film": {
            "film_id": null,
            "title": null,
            "description": null,
            "release_year": null,
            "language_id": null,
            "original_language_id": null,
            "rental_duration": null,
            "rental_rate": null,
            "length": null,
            "replacement_cost": null,
            "rating": null,
            "special_features": null,
            "last_update": null,
            "_language_id": "MANYTOONE",
            "_original_language_id": "MANYTOONE",
            "_film_actor": "ONETOMANY",
            "_inventory": "ONETOMANY"
        },
        "film_actor": {
            "actor_id": null,
            "film_id": null,
            "last_update": null,
            "_actor_id": "ONETOONE",
            "_film_id": "MANYTOONE"
        },
        "film_category": {
            "film_id": null,
            "category_id": null,
            "last_update": null,
            "_category_id": "MANYTOONE"
        },
        "film_text": {
            "film_id": null,
            "title": null,
            "description": null
        },
        "inventory": {
            "inventory_id": null,
            "film_id": null,
            "store_id": null,
            "last_update": null,
            "_film_id": "MANYTOONE",
            "_store_id": "MANYTOONE",
            "_rental": "ONETOMANY"
        },
        "language": {
            "language_id": null,
            "name": null,
            "last_update": null,
            "_film": "ONETOMANY"
        },
        "payment": {
            "payment_id": null,
            "customer_id": null,
            "staff_id": null,
            "rental_id": null,
            "amount": null,
            "payment_date": null,
            "last_update": null,
            "_customer_id": "MANYTOONE",
            "_rental_id": "MANYTOONE",
            "_staff_id": "MANYTOONE"
        },
        "rental": {
            "rental_id": null,
            "rental_date": null,
            "inventory_id": null,
            "customer_id": null,
            "return_date": null,
            "staff_id": null,
            "last_update": null,
            "_customer_id": "MANYTOONE",
            "_inventory_id": "MANYTOONE",
            "_staff_id": "MANYTOONE",
            "_payment": "ONETOMANY"
        },
        "staff": {
            "staff_id": null,
            "first_name": null,
            "last_name": null,
            "address_id": null,
            "picture": null,
            "email": null,
            "store_id": null,
            "active": null,
            "username": null,
            "password": null,
            "last_update": null,
            "_address_id": "MANYTOONE",
            "_store_id": "MANYTOONE",
            "_payment": "ONETOMANY",
            "_rental": "ONETOMANY",
            "_store": "ONETOMANY"
        },
        "store": {
            "store_id": null,
            "manager_staff_id": null,
            "address_id": null,
            "last_update": null,
            "_address_id": "MANYTOONE",
            "_manager_staff_id": "MANYTOONE",
            "_customer": "ONETOMANY",
            "_inventory": "ONETOMANY",
            "_staff": "ONETOMANY"
        }
    },
    "extracolumn": {
        "actor": [],
        "address": [],
        "category": [],
        "city": [],
        "country": [],
        "customer": [],
        "film": [],
        "film_actor": [],
        "film_category": [],
        "film_text": [],
        "inventory": [],
        "language": [],
        "payment": [],
        "rental": [],
        "staff": [],
        "store": []
    },
    "tablexclass": {
        "actor": "Actor",
        "address": "Addres",
        "category": "Category",
        "city": "City",
        "country": "Country",
        "customer": "Customer",
        "film": "Film",
        "film_actor": "FilmActor",
        "film_category": "FilmCategory",
        "film_text": "FilmText",
        "inventory": "Inventory",
        "language": "Language",
        "payment": "Payment",
        "rental": "Rental",
        "staff": "Staff",
        "store": "Store"
    },
    "removecolumn": [],
    "tablesmarked": [],
    "folder": {
        "classdirectory": "repo",
        "classpostfix": "Repo",
        "classnamespace": "eftec\\examples\\example2\\repo"
    }
}