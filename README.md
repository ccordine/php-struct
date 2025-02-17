The `Gryph\PHPStruct` library offers a robust and flexible solution for managing structured data in PHP. By leveraging PHP's reflection capabilities, this library enables developers to define classes with strict attribute typing and validation, ensuring data integrity and consistency across applications.

**Key Features:**

- **Dynamic Attribute Assignment:** Upon instantiation, the `Struct` class assigns values from an input array to the corresponding class properties. This dynamic assignment facilitates the seamless integration of data into structured objects.

- **Type Enforcement:** The library enforces strict type checking for class properties. If a property is assigned a value that doesn't match its declared type, an exception is thrown, preventing potential data type mismatches.

- **Nullable Support:** Developers can designate properties as nullable using the `Nullable` attribute or by specifying a nullable type (e.g., `?string`). This feature provides flexibility in handling optional attributes without compromising on type safety.

- **Comprehensive Property Analysis:** The `keys` method utilizes reflection to analyze and retrieve all properties of a class, categorizing them into public, private, and protected arrays. This comprehensive analysis aids in better property management and introspection.

- **Data Serialization:** The library offers methods to serialize the structured data into various formats:
  - `json()`: Converts the object into a JSON string.
  - `data()`: Returns a `stdClass` object representation.
  - `array()`: Provides an associative array representation.

- **Factory Method:** The static `generate` method allows for the creation of new instances of a class, initializing them with the provided attributes. This factory pattern promotes cleaner and more maintainable code when generating objects.

**Usage Example:**

```php
<?php

namespace Gryph\PHPStruct;

class User extends Struct
{
    public string $name;
    public int $age;
    #[Nullable]
    public ?string $email;
}

$userData = [
    'name' => 'Alice',
    'age' => 30,
    'email' => 'alice@example.com',
];

$user = User::generate($userData);

echo $user->json();
```

In this example, the `User` class extends the `Struct` base class, inheriting its dynamic assignment and validation features. The `Nullable` attribute allows the `email` property to be null, providing flexibility in object instantiation.

By incorporating `Gryph\PHPStruct` into your project, you can enhance data management, enforce strict typing, and ensure the reliability of your application's data structures. 
