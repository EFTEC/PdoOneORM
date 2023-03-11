<?php http_response_code(404); die(1); // eftec/CliOne configuration file ?>
{
    "help": false,
    "first": "generate",
    "definition": "",
    "type": null,
    "databasetype": "mysql",
    "server": "127.0.0.1",
    "user": "root",
    "password": "abc.123",
    "database": "sakila_lite",
    "classdirectory": "sakila2021",
    "classpostfix": "Repo",
    "classnamespace": "eftec\\tests\\sakila2021",
    "namespace": null,
    "savegen": "yes",
    "tables": null,
    "tablescolumns": null,
    "tablecommand": null,
    "convertionselected": null,
    "convertionnewvalue": null,
    "newclassname": null,
    "overridegenerate": "yes",
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
    "removecolumn": [],
    "columnsTable": {
        "actor": {
            "actor_id": null,
            "first_name": null,
            "last_name": null,
            "last_update": null,
            "_film_actor": "ONETOMANY"
        },
        "address": {
            "address": null,
            "address2": null,
            "address_id": null,
            "city_id": null,
            "district": null,
            "last_update": null,
            "phone": null,
            "postal_code": null,
            "_city_id": "MANYTOONE",
            "_customer": "ONETOMANY",
            "_staff": "ONETOMANY",
            "_store": "ONETOMANY"
        },
        "category": {
            "category_id": null,
            "last_update": null,
            "name": null,
            "_film_category": "ONETOMANY"
        },
        "city": {
            "city": null,
            "city_id": null,
            "country_id": null,
            "last_update": null,
            "_country_id": "MANYTOONE",
            "_address": "ONETOMANY"
        },
        "country": {
            "country": null,
            "country_id": null,
            "last_update": null,
            "_city": "ONETOMANY"
        },
        "customer": {
            "active": null,
            "address_id": null,
            "create_date": null,
            "customer_id": null,
            "email": null,
            "first_name": null,
            "last_name": null,
            "last_update": null,
            "store_id": null,
            "_address_id": "MANYTOONE",
            "_store_id": "MANYTOONE",
            "_payment": "ONETOMANY",
            "_rental": "ONETOMANY"
        },
        "film": {
            "description": null,
            "film_id": null,
            "language_id": null,
            "last_update": null,
            "length": null,
            "original_language_id": null,
            "rating": null,
            "release_year": null,
            "rental_duration": null,
            "rental_rate": null,
            "replacement_cost": null,
            "special_features": null,
            "title": null,
            "_language_id": "MANYTOONE",
            "_original_language_id": "MANYTOONE",
            "_film_actor": "ONETOMANY",
            "_film_text": "ONETOONE",
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
            "category_id": null,
            "film_id": null,
            "last_update": null,
            "_category_id": "MANYTOONE"
        },
        "film_text": {
            "description": null,
            "film_id": null,
            "title": null,
            "_film_id": "ONETOONE"
        },
        "inventory": {
            "film_id": null,
            "inventory_id": null,
            "last_update": null,
            "store_id": null,
            "_film_id": "MANYTOONE",
            "_store_id": "MANYTOONE",
            "_rental": "ONETOMANY"
        },
        "language": {
            "language_id": null,
            "last_update": null,
            "name": null,
            "_film": "ONETOMANY"
        },
        "payment": {
            "amount": null,
            "customer_id": null,
            "last_update": null,
            "payment_date": null,
            "payment_id": null,
            "rental_id": null,
            "staff_id": null,
            "_customer_id": "MANYTOONE",
            "_rental_id": "MANYTOONE",
            "_staff_id": "MANYTOONE"
        },
        "rental": {
            "customer_id": null,
            "inventory_id": null,
            "last_update": null,
            "rental_date": null,
            "rental_id": null,
            "return_date": null,
            "staff_id": null,
            "_customer_id": "MANYTOONE",
            "_inventory_id": "MANYTOONE",
            "_staff_id": "MANYTOONE",
            "_payment": "ONETOMANY"
        },
        "staff": {
            "active": null,
            "address_id": null,
            "email": null,
            "first_name": null,
            "last_name": null,
            "last_update": null,
            "password": null,
            "picture": null,
            "staff_id": null,
            "store_id": null,
            "username": null,
            "_store_id": "MANYTOONE",
            "_address_id": "MANYTOONE",
            "_payment": "ONETOMANY",
            "_rental": "ONETOMANY",
            "_store": "ONETOMANY"
        },
        "store": {
            "address_id": null,
            "last_update": null,
            "manager_staff_id": null,
            "store_id": null,
            "_staff": "ONETOMANY",
            "_address_id": "MANYTOONE",
            "_manager_staff_id": "MANYTOONE",
            "_customer": "ONETOMANY",
            "_inventory": "ONETOMANY"
        }
    },
    "columnsAlias": {
        "actor": {
            "actor_id": "actor_id",
            "first_name": "first_name",
            "last_name": "last_name",
            "last_update": "last_update",
            "_film_actor": "_film_actor"
        },
        "address": {
            "address": "address",
            "address2": "address2",
            "address_id": "address_id",
            "city_id": "city_id",
            "district": "district",
            "last_update": "last_update",
            "phone": "phone",
            "postal_code": "postal_code",
            "_city_id": "_city_id",
            "_customer": "_customer",
            "_staff": "_staff",
            "_store": "_store"
        },
        "category": {
            "category_id": "category_id",
            "last_update": "last_update",
            "name": "name",
            "_film_category": "_film_category"
        },
        "city": {
            "city": "city",
            "city_id": "city_id",
            "country_id": "country_id",
            "last_update": "last_update",
            "_country_id": "_country_id",
            "_address": "_address"
        },
        "country": {
            "country": "country",
            "country_id": "country_id",
            "last_update": "last_update",
            "_city": "_city"
        },
        "customer": {
            "active": "active",
            "address_id": "address_id",
            "create_date": "create_date",
            "customer_id": "customer_id",
            "email": "email",
            "first_name": "first_name",
            "last_name": "last_name",
            "last_update": "last_update",
            "store_id": "store_id",
            "_address_id": "_address_id",
            "_store_id": "_store_id",
            "_payment": "_payment",
            "_rental": "_rental"
        },
        "film": {
            "description": "description",
            "film_id": "film_id",
            "language_id": "language_id",
            "last_update": "last_update",
            "length": "length",
            "original_language_id": "original_language_id",
            "rating": "rating",
            "release_year": "release_year",
            "rental_duration": "rental_duration",
            "rental_rate": "rental_rate",
            "replacement_cost": "replacement_cost",
            "special_features": "special_features",
            "title": "title",
            "_language_id": "_language_id",
            "_original_language_id": "_original_language_id",
            "_film_actor": "_film_actor",
            "_film_text": "_film_text",
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
            "category_id": "category_id",
            "film_id": "film_id",
            "last_update": "last_update",
            "_category_id": "_category_id"
        },
        "film_text": {
            "description": "description",
            "film_id": "film_id",
            "title": "title",
            "_film_id": "_film_id"
        },
        "inventory": {
            "film_id": "film_id",
            "inventory_id": "inventory_id",
            "last_update": "last_update",
            "store_id": "store_id",
            "_film_id": "_film_id",
            "_store_id": "_store_id",
            "_rental": "_rental"
        },
        "language": {
            "language_id": "language_id",
            "last_update": "last_update",
            "name": "name",
            "_film": "_film"
        },
        "payment": {
            "amount": "amount",
            "customer_id": "customer_id",
            "last_update": "last_update",
            "payment_date": "payment_date",
            "payment_id": "payment_id",
            "rental_id": "rental_id",
            "staff_id": "staff_id",
            "_customer_id": "_customer_id",
            "_rental_id": "_rental_id",
            "_staff_id": "_staff_id"
        },
        "rental": {
            "customer_id": "customer_id",
            "inventory_id": "inventory_id",
            "last_update": "last_update",
            "rental_date": "rental_date",
            "rental_id": "rental_id",
            "return_date": "return_date",
            "staff_id": "staff_id",
            "_customer_id": "_customer_id",
            "_inventory_id": "_inventory_id",
            "_staff_id": "_staff_id",
            "_payment": "_payment"
        },
        "staff": {
            "active": "active",
            "address_id": "address_id",
            "email": "email",
            "first_name": "first_name",
            "last_name": "last_name",
            "last_update": "last_update",
            "password": "password",
            "picture": "picture",
            "staff_id": "staff_id",
            "store_id": "store_id",
            "username": "username",
            "_store_id": "_store_id",
            "_address_id": "_address_id",
            "_payment": "_payment",
            "_rental": "_rental",
            "_store": "_store"
        },
        "store": {
            "address_id": "address_id",
            "last_update": "last_update",
            "manager_staff_id": "manager_staff_id",
            "store_id": "store_id",
            "_staff": "_staff",
            "_address_id": "_address_id",
            "_manager_staff_id": "_manager_staff_id",
            "_customer": "_customer",
            "_inventory": "_inventory"
        }
    }
}