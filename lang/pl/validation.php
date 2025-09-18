<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
    'accepted_if' => 'Pole :attribute musi zostać zaakceptowane gdy :other jest :value.',
    'active_url' => 'Pole :attribute musi być prawidłowym adresem URL.',
    'after' => 'Pole :attribute musi być datą późniejszą niż :date.',
    'after_or_equal' => 'Pole :attribute musi być datą późniejszą lub równą :date.',
    'alpha' => 'Pole :attribute może zawierać tylko litery.',
    'alpha_dash' => 'Pole :attribute może zawierać tylko litery, cyfry, myślniki i podkreślenia.',
    'alpha_num' => 'Pole :attribute może zawierać tylko litery i cyfry.',
    'any_of' => 'Pole :attribute jest nieprawidłowe.',
    'array' => 'Pole :attribute musi być tablicą.',
    'ascii' => 'Pole :attribute może zawierać tylko jednobajtowe znaki alfanumeryczne i symbole.',
    'before' => 'Pole :attribute musi być datą wcześniejszą niż :date.',
    'before_or_equal' => 'Pole :attribute musi być datą wcześniejszą lub równą :date.',
    'between' => [
        'array' => 'Pole :attribute musi zawierać od :min do :max elementów.',
        'file' => 'Plik :attribute musi mieć od :min do :max kilobajtów.',
        'numeric' => 'Pole :attribute musi być między :min a :max.',
        'string' => 'Pole :attribute musi zawierać od :min do :max znaków.',
    ],
    'boolean' => 'Pole :attribute musi być prawdą lub fałszem.',
    'can' => 'Pole :attribute zawiera niedozwoloną wartość.',
    'confirmed' => 'Potwierdzenie pola :attribute nie pasuje.',
    'contains' => 'W polu :attribute brakuje wymaganej wartości.',
    'current_password' => 'Hasło jest nieprawidłowe.',
    'date' => 'Pole :attribute musi być prawidłową datą.',
    'date_equals' => 'Pole :attribute musi być datą równą :date.',
    'date_format' => 'Pole :attribute musi pasować do formatu :format.',
    'decimal' => 'Pole :attribute musi mieć :decimal miejsc po przecinku.',
    'declined' => 'Pole :attribute musi zostać odrzucone.',
    'declined_if' => 'Pole :attribute musi zostać odrzucone gdy :other jest :value.',
    'different' => 'Pole :attribute i :other muszą się różnić.',
    'digits' => 'Pole :attribute musi mieć :digits cyfr.',
    'digits_between' => 'Pole :attribute musi mieć od :min do :max cyfr.',
    'dimensions' => 'Pole :attribute ma nieprawidłowe wymiary obrazu.',
    'distinct' => 'Pole :attribute ma zduplikowaną wartość.',
    'doesnt_end_with' => 'Pole :attribute nie może kończyć się jednym z następujących: :values.',
    'doesnt_start_with' => 'Pole :attribute nie może zaczynać się od jednego z następujących: :values.',
    'email' => 'Pole :attribute musi być prawidłowym adresem email.',
    'ends_with' => 'Pole :attribute musi kończyć się jednym z następujących: :values.',
    'enum' => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'exists' => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'extensions' => 'Pole :attribute musi mieć jedno z następujących rozszerzeń: :values.',
    'file' => 'Pole :attribute musi być plikiem.',
    'filled' => 'Pole :attribute musi mieć wartość.',
    'gt' => [
        'array' => 'Pole :attribute musi mieć więcej niż :value elementów.',
        'file' => 'Plik :attribute musi być większy niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być większe niż :value.',
        'string' => 'Pole :attribute musi mieć więcej niż :value znaków.',
    ],
    'gte' => [
        'array' => 'Pole :attribute musi mieć :value elementów lub więcej.',
        'file' => 'Plik :attribute musi mieć :value kilobajtów lub więcej.',
        'numeric' => 'Pole :attribute musi być większe lub równe :value.',
        'string' => 'Pole :attribute musi mieć :value znaków lub więcej.',
    ],
    'hex_color' => 'Pole :attribute musi być prawidłowym kolorem szesnastkowym.',
    'image' => 'Pole :attribute musi być obrazem.',
    'in' => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'in_array' => 'Pole :attribute musi istnieć w :other.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'ip' => 'Pole :attribute musi być prawidłowym adresem IP.',
    'ipv4' => 'Pole :attribute musi być prawidłowym adresem IPv4.',
    'ipv6' => 'Pole :attribute musi być prawidłowym adresem IPv6.',
    'json' => 'Pole :attribute musi być prawidłowym ciągiem JSON.',
    'list' => 'Pole :attribute musi być listą.',
    'lowercase' => 'Pole :attribute musi być napisane małymi literami.',
    'lt' => [
        'array' => 'Pole :attribute musi mieć mniej niż :value elementów.',
        'file' => 'Plik :attribute musi być mniejszy niż :value kilobajtów.',
        'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
        'string' => 'Pole :attribute musi mieć mniej niż :value znaków.',
    ],
    'lte' => [
        'array' => 'Pole :attribute nie może mieć więcej niż :value elementów.',
        'file' => 'Plik :attribute musi mieć :value kilobajtów lub mniej.',
        'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
        'string' => 'Pole :attribute musi mieć :value znaków lub mniej.',
    ],
    'mac_address' => 'Pole :attribute musi być prawidłowym adresem MAC.',
    'max' => [
        'array' => 'Pole :attribute nie może mieć więcej niż :max elementów.',
        'file' => 'Plik :attribute nie może być większy niż :max kilobajtów.',
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'string' => 'Pole :attribute nie może mieć więcej niż :max znaków.',
    ],
    'max_digits' => 'Pole :attribute nie może mieć więcej niż :max cyfr.',
    'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
    'mimetypes' => 'Pole :attribute musi być plikiem typu: :values.',
    'min' => [
        'array' => 'Pole :attribute musi mieć przynajmniej :min elementów.',
        'file' => 'Plik :attribute musi mieć przynajmniej :min kilobajtów.',
        'numeric' => 'Pole :attribute musi być przynajmniej :min.',
        'string' => 'Pole :attribute musi mieć przynajmniej :min znaków.',
    ],
    'min_digits' => 'Pole :attribute musi mieć przynajmniej :min cyfr.',
    'missing' => 'Pole :attribute musi być nieobecne.',
    'missing_if' => 'Pole :attribute musi być nieobecne gdy :other jest :value.',
    'missing_unless' => 'Pole :attribute musi być nieobecne chyba że :other jest :value.',
    'missing_with' => 'Pole :attribute musi być nieobecne gdy obecne jest :values.',
    'missing_with_all' => 'Pole :attribute musi być nieobecne gdy obecne są :values.',
    'multiple_of' => 'Pole :attribute musi być wielokrotnością :value.',
    'not_in' => 'Wybrana wartość :attribute jest nieprawidłowa.',
    'not_regex' => 'Format pola :attribute jest nieprawidłowy.',
    'numeric' => 'Pole :attribute musi być liczbą.',
    'password' => [
        'letters' => 'Pole :attribute musi zawierać przynajmniej jedną literę.',
        'mixed' => 'Pole :attribute musi zawierać przynajmniej jedną wielką i jedną małą literę.',
        'numbers' => 'Pole :attribute musi zawierać przynajmniej jedną cyfrę.',
        'symbols' => 'Pole :attribute musi zawierać przynajmniej jeden symbol.',
        'uncompromised' => 'Podane :attribute pojawiło się w wycieku danych. Proszę wybrać inne :attribute.',
    ],
    'present' => 'Pole :attribute musi być obecne.',
    'present_if' => 'Pole :attribute musi być obecne gdy :other jest :value.',
    'present_unless' => 'Pole :attribute musi być obecne chyba że :other jest :value.',
    'present_with' => 'Pole :attribute musi być obecne gdy obecne jest :values.',
    'present_with_all' => 'Pole :attribute musi być obecne gdy obecne są :values.',
    'prohibited' => 'Pole :attribute jest zabronione.',
    'prohibited_if' => 'Pole :attribute jest zabronione gdy :other jest :value.',
    'prohibited_unless' => 'Pole :attribute jest zabronione chyba że :other jest w :values.',
    'prohibits' => 'Pole :attribute zabrania obecności :other.',
    'regex' => 'Format pola :attribute jest nieprawidłowy.',
    'required' => 'Pole :attribute jest wymagane.',
    'required_array_keys' => 'Pole :attribute musi zawierać wpisy dla: :values.',
    'required_if' => 'Pole :attribute jest wymagane gdy :other jest :value.',
    'required_if_accepted' => 'Pole :attribute jest wymagane gdy :other jest zaakceptowane.',
    'required_if_declined' => 'Pole :attribute jest wymagane gdy :other jest odrzucone.',
    'required_unless' => 'Pole :attribute jest wymagane chyba że :other jest w :values.',
    'required_with' => 'Pole :attribute jest wymagane gdy obecne jest :values.',
    'required_with_all' => 'Pole :attribute jest wymagane gdy obecne są :values.',
    'required_without' => 'Pole :attribute jest wymagane gdy :values nie jest obecne.',
    'required_without_all' => 'Pole :attribute jest wymagane gdy żadne z :values nie są obecne.',
    'same' => 'Pole :attribute musi pasować do :other.',
    'size' => [
        'array' => 'Pole :attribute musi zawierać :size elementów.',
        'file' => 'Plik :attribute musi mieć :size kilobajtów.',
        'numeric' => 'Pole :attribute musi być :size.',
        'string' => 'Pole :attribute musi mieć :size znaków.',
    ],
    'starts_with' => 'Pole :attribute musi zaczynać się od jednego z następujących: :values.',
    'string' => 'Pole :attribute musi być ciągiem znaków.',
    'timezone' => 'Pole :attribute musi być prawidłową strefą czasową.',
    'unique' => 'Pole :attribute zostało już zajęte.',
    'uploaded' => 'Nie udało się przesłać :attribute.',
    'uppercase' => 'Pole :attribute musi być napisane wielkimi literami.',
    'url' => 'Pole :attribute musi być prawidłowym adresem URL.',
    'ulid' => 'Pole :attribute musi być prawidłowym ULID.',
    'uuid' => 'Pole :attribute musi być prawidłowym UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nazwa',
        'username' => 'nazwa użytkownika',
        'email' => 'adres email',
        'password' => 'hasło',
        'password_confirmation' => 'potwierdzenie hasła',
        'city' => 'miasto',
        'country' => 'kraj',
        'address' => 'adres',
        'phone' => 'telefon',
        'mobile' => 'telefon komórkowy',
        'age' => 'wiek',
        'sex' => 'płeć',
        'gender' => 'płeć',
        'day' => 'dzień',
        'month' => 'miesiąc',
        'year' => 'rok',
        'hour' => 'godzina',
        'minute' => 'minuta',
        'second' => 'sekunda',
        'title' => 'tytuł',
        'content' => 'treść',
        'description' => 'opis',
        'excerpt' => 'fragment',
        'date' => 'data',
        'time' => 'czas',
        'available' => 'dostępny',
        'size' => 'rozmiar',
        'file' => 'plik',
        'image' => 'obraz',
        'photo' => 'zdjęcie',
        'avatar' => 'awatar',
        'subject' => 'temat',
        'message' => 'wiadomość',
        'first_name' => 'imię',
        'last_name' => 'nazwisko',
        'location' => 'lokalizacja',
        'price' => 'cena',
        'category' => 'kategoria',
        'pet_type' => 'typ zwierzęcia',
        'pet_size' => 'rozmiar zwierzęcia',
        'service_type' => 'typ usługi',
        'rating' => 'ocena',
    ],

];