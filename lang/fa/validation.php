<?php

/*
|--------------------------------------------------------------------------
| خطوط زبان اعتبارسنجی (دری)
|--------------------------------------------------------------------------
|
| این فایل پیام‌های خطای اعتبارسنجی را به دری برمی‌گرداند. کلیدهای قاعده
| (required، email، ...) استاندارد لاراول‌اند و placeholder :attribute با
| نام دری فیلد (از آرایهٔ attributes پایین) جایگزین می‌شود.
|
*/

return [

    'accepted' => 'باید :attribute پذیرفته شود.',
    'accepted_if' => 'وقتی :other برابر :value باشد، باید :attribute پذیرفته شود.',
    'active_url' => ':attribute یک نشانی معتبر نیست.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد از یا برابر :date باشد.',
    'alpha' => ':attribute فقط می‌تواند شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط می‌تواند شامل حروف و اعداد باشد.',
    'array' => ':attribute باید یک آرایه باشد.',
    'ascii' => ':attribute فقط می‌تواند شامل حروف و نمادهای انگلیسی باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل از یا برابر :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min و :max مورد داشته باشد.',
        'file' => 'حجم :attribute باید بین :min و :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min و :max باشد.',
        'string' => ':attribute باید بین :min و :max حرف باشد.',
    ],
    'boolean' => 'مقدار :attribute باید بلی یا خیر باشد.',
    'can' => ':attribute شامل یک مقدار غیرمجاز است.',
    'confirmed' => 'تأییدیهٔ :attribute مطابقت ندارد.',
    'contains' => ':attribute فاقد یک مقدار الزامی است.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => ':attribute یک تاریخ معتبر نیست.',
    'date_equals' => ':attribute باید تاریخی برابر با :date باشد.',
    'date_format' => ':attribute با قالب :format مطابقت ندارد.',
    'decimal' => ':attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => 'وقتی :other برابر :value باشد، باید :attribute رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min و :max رقم باشد.',
    'dimensions' => 'ابعاد تصویر :attribute نامعتبر است.',
    'distinct' => 'فیلد :attribute مقدار تکراری دارد.',
    'doesnt_end_with' => ':attribute نباید با یکی از این‌ها پایان یابد: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از این‌ها شروع شود: :values.',
    'email' => ':attribute باید یک نشانی ایمیل معتبر باشد.',
    'ends_with' => ':attribute باید با یکی از این‌ها پایان یابد: :values.',
    'enum' => 'مقدار انتخاب‌شده برای :attribute نامعتبر است.',
    'exists' => 'مقدار انتخاب‌شده برای :attribute نامعتبر است.',
    'extensions' => ':attribute باید یکی از این پسوندها را داشته باشد: :values.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => 'فیلد :attribute باید مقدار داشته باشد.',
    'gt' => [
        'array' => ':attribute باید بیش از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید بیشتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر از :value باشد.',
        'string' => ':attribute باید بیشتر از :value حرف باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید :value مورد یا بیشتر داشته باشد.',
        'file' => 'حجم :attribute باید بزرگ‌تر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر یا برابر :value باشد.',
        'string' => ':attribute باید بزرگ‌تر یا برابر :value حرف باشد.',
    ],
    'hex_color' => ':attribute باید یک رنگ hex معتبر باشد.',
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => 'مقدار انتخاب‌شده برای :attribute نامعتبر است.',
    'in_array' => 'فیلد :attribute در :other وجود ندارد.',
    'integer' => ':attribute باید یک عدد صحیح باشد.',
    'ip' => ':attribute باید یک نشانی IP معتبر باشد.',
    'ipv4' => ':attribute باید یک نشانی IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک نشانی IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشتهٔ JSON معتبر باشد.',
    'lowercase' => ':attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچک‌تر از :value باشد.',
        'string' => ':attribute باید کمتر از :value حرف باشد.',
    ],
    'lte' => [
        'array' => ':attribute باید حداکثر :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کوچک‌تر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچک‌تر یا برابر :value باشد.',
        'string' => ':attribute باید کوچک‌تر یا برابر :value حرف باشد.',
    ],
    'mac_address' => ':attribute باید یک نشانی MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیش از :max مورد داشته باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگ‌تر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max حرف باشد.',
    ],
    'max_digits' => ':attribute نباید بیش از :max رقم داشته باشد.',
    'mimes' => ':attribute باید فایلی از نوع :values باشد.',
    'mimetypes' => ':attribute باید فایلی از نوع :values باشد.',
    'min' => [
        'array' => ':attribute باید حداقل :min مورد داشته باشد.',
        'file' => 'حجم :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => ':attribute باید حداقل :min باشد.',
        'string' => ':attribute باید حداقل :min حرف باشد.',
    ],
    'min_digits' => ':attribute باید حداقل :min رقم داشته باشد.',
    'missing' => 'فیلد :attribute باید حذف شود.',
    'missing_if' => 'وقتی :other برابر :value باشد، فیلد :attribute باید حذف شود.',
    'missing_unless' => 'مگر آنکه :other برابر :value باشد، فیلد :attribute باید حذف شود.',
    'missing_with' => 'وقتی :values موجود باشد، فیلد :attribute باید حذف شود.',
    'missing_with_all' => 'وقتی :values موجود باشند، فیلد :attribute باید حذف شود.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => 'مقدار انتخاب‌شده برای :attribute نامعتبر است.',
    'not_regex' => 'قالب :attribute نامعتبر است.',
    'numeric' => ':attribute باید یک عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حداقل یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در نشت داده‌ها ظاهر شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'present' => 'فیلد :attribute باید موجود باشد.',
    'present_if' => 'وقتی :other برابر :value باشد، فیلد :attribute باید موجود باشد.',
    'present_unless' => 'مگر آنکه :other برابر :value باشد، فیلد :attribute باید موجود باشد.',
    'present_with' => 'وقتی :values موجود باشد، فیلد :attribute باید موجود باشد.',
    'present_with_all' => 'وقتی :values موجود باشند، فیلد :attribute باید موجود باشد.',
    'prohibited' => 'فیلد :attribute ممنوع است.',
    'prohibited_if' => 'وقتی :other برابر :value باشد، فیلد :attribute ممنوع است.',
    'prohibited_unless' => 'مگر آنکه :other در :values باشد، فیلد :attribute ممنوع است.',
    'prohibits' => 'فیلد :attribute مانع موجود بودن :other می‌شود.',
    'regex' => 'قالب :attribute نامعتبر است.',
    'required' => 'فیلد :attribute الزامی است.',
    'required_array_keys' => 'فیلد :attribute باید شامل این کلیدها باشد: :values.',
    'required_if' => 'وقتی :other برابر :value باشد، فیلد :attribute الزامی است.',
    'required_if_accepted' => 'وقتی :other پذیرفته شود، فیلد :attribute الزامی است.',
    'required_if_declined' => 'وقتی :other رد شود، فیلد :attribute الزامی است.',
    'required_unless' => 'مگر آنکه :other در :values باشد، فیلد :attribute الزامی است.',
    'required_with' => 'وقتی :values موجود باشد، فیلد :attribute الزامی است.',
    'required_with_all' => 'وقتی :values موجود باشند، فیلد :attribute الزامی است.',
    'required_without' => 'وقتی :values موجود نباشد، فیلد :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ‌یک از :values موجود نباشند، فیلد :attribute الزامی است.',
    'same' => ':attribute و :other باید یکسان باشند.',
    'size' => [
        'array' => ':attribute باید شامل :size مورد باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید برابر :size باشد.',
        'string' => ':attribute باید :size حرف باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از این‌ها شروع شود: :values.',
    'string' => ':attribute باید یک متن باشد.',
    'timezone' => ':attribute باید یک منطقهٔ زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً استفاده شده است.',
    'uploaded' => 'بارگذاری :attribute ناموفق بود.',
    'uppercase' => ':attribute باید با حروف بزرگ باشد.',
    'url' => ':attribute باید یک نشانی معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | پیام‌های اعتبارسنجی سفارشی
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | نام دری فیلدها
    |--------------------------------------------------------------------------
    |
    | این نام‌ها جای :attribute را در پیام‌های خطا می‌گیرند تا خطاها به‌جای
    | نام انگلیسی فیلد، عنوان دری فرم را نشان دهند.
    |
    */

    'attributes' => [
        // مشترک
        'name' => 'نام',
        'company' => 'شرکت',
        'company_name' => 'نام شرکت',
        'email' => 'ایمیل',
        'phone' => 'تلفن',
        'password' => 'رمز عبور',
        'remember' => 'مرا به خاطر بسپار',
        'address' => 'آدرس',
        'billing_address' => 'آدرس صورت‌حساب',
        'site_address' => 'آدرس محل کار',
        'notes' => 'یادداشت‌ها',
        'description' => 'توضیحات',
        'status' => 'وضعیت',
        'type' => 'نوع',
        'reference' => 'مرجع',
        'tax_id' => 'شناسهٔ مالیاتی',
        'tax_rate' => 'نرخ مالیات',
        'discount' => 'تخفیف',
        'amount' => 'مبلغ',
        'title' => 'عنوان',
        'category' => 'دسته‌بندی',
        'unit' => 'واحد',
        'unit_price' => 'قیمت واحد',

        // مشتری / برآورد / فاکتور
        'customer_id' => 'مشتری',
        'issue_date' => 'تاریخ صدور',
        'valid_until' => 'معتبر تا',
        'due_date' => 'تاریخ سررسید',
        'terms' => 'شرایط',

        // کار / پروژه
        'project_id' => 'کار',
        'start_date' => 'تاریخ شروع',
        'assigned_to' => 'مسئول',
        'budget' => 'بودجه',

        // تأمین‌کننده / خرید (بل)
        'supplier_id' => 'تأمین‌کننده',
        'bill_date' => 'تاریخ بل',

        // حساب / تراکنش
        'account_id' => 'حساب',
        'to_account_id' => 'حساب مقصد',
        'category_id' => 'دسته‌بندی',
        'occurred_on' => 'تاریخ',
        'opening_balance' => 'موجودی اولیه',
        'is_default' => 'حساب پیش‌فرض',

        // کارمند / معاش
        'role' => 'وظیفه',
        'salary_type' => 'نوع معاش',
        'salary_rate' => 'میزان معاش',
        'joined_on' => 'تاریخ استخدام',
        'is_active' => 'فعال',

        // پرداخت
        'payAmount' => 'مبلغ پرداخت',
        'payDate' => 'تاریخ پرداخت',
        'payMethod' => 'روش پرداخت',
        'payAccountId' => 'حساب پرداخت',
        'payReference' => 'مرجع پرداخت',
        'payNote' => 'یادداشت پرداخت',

        // لیست حقوق
        'prEmployeeId' => 'کارمند',
        'periodLabel' => 'دورهٔ حقوق',
        'payrollBase' => 'معاش پایه',
        'payrollBonus' => 'پاداش',
        'payrollOvertime' => 'اضافه‌کاری',
        'payrollDeductions' => 'کسورات',
        'payrollAdvance' => 'مساعده',
        'advEmployeeId' => 'کارمند',
        'advAmount' => 'مبلغ مساعده',
        'advAccountId' => 'حساب',
        'advDate' => 'تاریخ',
        'advNote' => 'یادداشت',

        // تقویم / وقت
        'starts_at' => 'زمان شروع',
        'ends_at' => 'زمان پایان',

        // اقلام سطری (برآورد/فاکتور)
        'items' => 'اقلام',
        'items.*.description' => 'شرح قلم',
        'items.*.quantity' => 'مقدار',
        'items.*.qty' => 'مقدار',
        'items.*.unit_price' => 'قیمت واحد',
        'items.*.material_id' => 'متریال',

        // هزینه/وقت پروژه
        'expType' => 'نوع هزینه',
        'expDescription' => 'شرح هزینه',
        'expQty' => 'مقدار',
        'expUnitCost' => 'قیمت واحد',
        'apptTitle' => 'عنوان وقت',
        'apptStart' => 'شروع',
        'apptEnd' => 'پایان',
        'newTask' => 'کار جدید',

        // گزارش‌ها
        'asOf' => 'تا تاریخ',
        'from' => 'از تاریخ',
        'to' => 'تا تاریخ',
        'month' => 'ماه',
        'year' => 'سال',
    ],

];
