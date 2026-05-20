# Infrastruktur Project RS Unknown

Dokumen ini menjelaskan arsitektur dan infrastruktur dari project RS Unknown, termasuk sistem autentikasi, struktur folder, dan fungsi-fungsi utama backend.

## 1. Stack Teknologi

### Backend
- **Framework**: Laravel 12.56 - PHP web framework modern
- **Database**: PostgreSQL - Relational database
- **Authentication**: Laravel Fortify - Sistem autentikasi bawaan Laravel dengan 2FA support
- **Authorization**: Spatie Permission - Role dan permission management
- **Activity Logging**: Spatie Activity Log - Mencatat setiap perubahan data
- **Testing**: Pest - Modern PHP testing framework
- **Code Quality**: Pint - PHP code fixer

### Frontend
- **UI Framework**: Livewire 4.1 - Real-time reactive components
- **Component Library**: Flux 2.13.1 - Flux UI components untuk Livewire
- **CSS Framework**: Tailwind CSS 4.0.7 - Utility-first CSS
- **Build Tool**: Vite 8.0.0 - Frontend build tool
- **Additional**: ApexCharts untuk grafik, Tom Select untuk dropdown

### DevOps
- **Container**: Laravel Sail (optional)
- **Queue**: Database-based queue
- **Cache**: Database-based cache
- **Session**: Database-based session
- **File Storage**: Local filesystem

## 2. Sistem Autentikasi

### Overview
Project menggunakan **Laravel Fortify** yang menyediakan backend untuk fitur autentikasi termasuk:
- Login/Register
- Email Verification
- Password Reset
- Two-Factor Authentication (2FA)

### Flow Autentikasi

```
User Request
    ↓
Session Middleware Check
    ↓
Auth Guard (web) → Eloquent Provider → User Model
    ↓
Authorized atau Redirect to Login
```

### Konfigurasi Auth
**File**: `config/auth.php`

```php
'guards' => [
    'web' => [
        'driver' => 'session',      // Session-based authentication
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',     // Menggunakan Eloquent ORM
        'model' => User::class,     // User Model
    ],
],
```

### User Model
**File**: `app/Models/User.php`

Traits yang digunakan:
- `HasFactory` - untuk factory testing
- `Notifiable` - untuk notification system
- `TwoFactorAuthenticatable` - untuk 2FA
- `HasUuids` - menggunakan UUID sebagai primary key
- `HasRoles` - dari Spatie Permission untuk role management

User attributes:
```php
'name'              // Nama lengkap user
'email'             // Email unik
'password'          // Password (di-hash)
'timezone'          // Zona waktu user
'two_factor_secret' // Secret untuk 2FA (hidden)
'email_verified_at' // Timestamp verifikasi email
```

### Middleware

**`app/Http/Middleware/SetUserTimezone.php`**
- Set timezone dari user profile atau fallback ke app timezone
- Memastikan semua timestamp konsisten dengan timezone user
- Handle jika user belum login (guest)

```php
public function handle(Request $request, Closure $next)
{
    $timezone = Auth::user()?->timezone ?? config('app.timezone');
    
    if ($timezone) {
        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);
    }
    
    return $next($request);
}
```

**Note**: Middleware Authenticate dan RedirectIfAuthenticated adalah built-in Laravel, tidak di-override di project ini

## 3. Sistem Role & Permission

### Package: Spatie Permission

Project menggunakan Spatie Laravel Permission untuk manajemen role dan permission.

### Implementasi di User Model

```php
class User extends Authenticatable {
    use HasRoles;
    // ...
}
```

### Roles yang Tersedia

| Role | Deskripsi |
|------|-----------|
| **admin** | Full akses ke semua fitur aplikasi |
| **marketing** | Akses dashboard dan management data marketing |
| **cashier** | Akses transaksi dan operasional kasir |

### Permission Check

**Di Route** (file: `routes/web.php`):
```php
Route::middleware(['auth', 'role:cashier'])
    ->group(function () {
        // Hanya user dengan role cashier yang bisa akses
    });
```

**Di Controller/Livewire Component**:
```php
// Check role
if ($user->hasRole('cashier')) {
    // Do something
}

// Check permission
if ($user->hasPermissionTo('edit_transaction')) {
    // Do something
}
```

**Di View (Blade)**:
```blade
@role('cashier')
    <!-- Tampilkan hanya untuk cashier -->
@endrole

@can('edit_transaction')
    <!-- Tampilkan jika user bisa edit transaction -->
@endcan
```

## 4. Activity Logging

### Package: Spatie Activity Log

Setiap perubahan data dicatat untuk audit trail.

### Implementasi

Di model, tambahkan trait:
```php
use Spatie\ActivityLog\Traits\LogsActivity;

class Transaction extends Model {
    use LogsActivity;
    
    protected static $logFillable = true;
    protected static $logName = 'transaction';
}
```

### Akses Logs

```php
// Get activities untuk specific model
$transaction->activities;

// Get all activities
Activity::all();

// Filter by causer (user yang melakukan action)
Activity::where('causer_id', $userId)->get();
```

## 5. Struktur Folder Backend

### `/app` - Logika Aplikasi

#### **`Actions/`** - Action Classes
Pattern untuk business logic yang reusable across controllers dan Livewire components.

Struktur dan file yang ada:
```
Actions/
├── Auth/                       # Aksi terkait autentikasi
├── Dashboard/
│   └── GetDashboardSummaryAction.php      # Fetch dashboard summary data
├── Fortify/                    # Aksi Fortify authentication
├── Transaction/
│   ├── CreateTransactionAction.php        # Create transaction dengan kalkulasi
│   ├── UpdateTransactionAction.php        # Update transaction
│   └── DownloadInvoiceAction.php         # Generate & download invoice
└── Voucher/
    ├── CreateVoucherAction.php            # Create voucher baru
    ├── UpdateVoucherAction.php            # Update voucher
    └── DeleteVoucherAction.php            # Delete voucher
```

**Contoh**: `GetDashboardSummaryAction`
```php
class GetDashboardSummaryAction {
    public function execute(): array {
        // Get dashboard summary data
        return [
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::sum('final_amount'),
        ];
    }
}
```

**Contoh**: `CreateTransactionAction`
```php
class CreateTransactionAction {
    public function __construct(
        private VoucherService $voucherService
    ) {}
    
    public function execute(CreateTransactionData $data): Transaction {
        // Validate voucher & calculate discount
        if ($data->voucher_code) {
            $voucher = Voucher::where('code', $data->voucher_code)->first();
            $discount = $this->voucherService->calculate($voucher, $data->total_amount);
        }
        
        // Create transaction dengan discount
        $transaction = Transaction::create([
            'total_amount' => $data->total_amount,
            'discount_amount' => $discount ?? 0,
            'final_amount' => ($data->total_amount - ($discount ?? 0)),
        ]);
        
        // Create transaction items
        foreach ($data->items as $item) {
            $transaction->items()->create($item);
        }
        
        return $transaction;
    }
}
```

#### **`Models/`** - Eloquent Models
Representasi dari database tables sebagai PHP objects. Models handle database queries, relationships, dan business logic.

Models yang ada:
- `User.php` - User model dengan Fortify & Spatie traits
- `Transactions.php` - Transaksi pelanggan dengan activity logging
- `TransactionItem.php` - Item detail dari transaksi
- `Voucher.php` - Data voucher/diskon

**Contoh**: `Transactions` model
```php
class Transactions extends Model {
    use HasUuids, LogsActivity;  // UUID primary key & activity logging
    
    protected $fillable = [
        'invoice_number',
        'patient_name',
        'patient_email',
        'patient_phone',
        'subtotal',
        'discount_total',
        'grand_total',
        'paid_at',
        'cashier_id',
        'status',
    ];
    
    protected function casts(): array {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_at' => 'datetime',
            'status' => TransactionStatus::class,  // Enum casting
        ];
    }
    
    // Relationships
    public function items(): HasMany {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }
    
    public function cashier(): BelongsTo {
        return $this->belongsTo(User::class, 'cashier_id');
    }
    
    // Activity Logging
    public function getActivitylogOptions(): LogOptions {
        return LogOptions::defaults()
            ->useLogName('transactions')
            ->logOnly(['invoice_number', 'patient_name', 'status']);
    }
}
```

**Contoh**: `TransactionItem` model
```php
class TransactionItem extends Model {
    protected $fillable = [
        'transaction_id',
        'procedure_id',
        'procedure_name',
        'quantity',
        'price',
        'subtotal',
    ];
    
    public function transaction(): BelongsTo {
        return $this->belongsTo(Transactions::class);
    }
}
```

**Key Features**:
- `HasUuids` trait untuk UUID primary keys
- `LogsActivity` trait untuk audit trail
- Type casting untuk ensure data types
- Enum casting untuk `TransactionStatus`
- Relationships (HasMany, BelongsTo)

#### **`Http/Controllers/`** - HTTP Controllers
Handle HTTP requests dan mengembalikan responses. Controllers menggunakan Dependency Injection untuk inject Actions dan Services.

Controllers yang ada:
- `DashboardController` - Display dashboard dengan conditional logic berdasarkan role
- `TransactionController` - Handle transaction store, update, delete
- `VoucherController` - Handle voucher operations
- `Controller.php` - Base controller class

**Contoh**: `DashboardController`
```php
class DashboardController extends Controller {
    public function __construct(
        private readonly GetDashboardSummaryAction $action,
    ) {}
    
    public function __invoke(): View {
        $user = Auth::user();
        
        // Different dashboard untuk role berbeda
        if ($user->hasRole('cashier')) {
            $transactions = Transactions::latest('created_at')->paginate(10);
            return view('dashboard.cashier', compact('transactions'));
        }
        
        return view('dashboard.marketing', $this->action->execute());
    }
}
```

**Contoh**: `TransactionController`
```php
class TransactionController extends Controller {
    public function __construct(
        private readonly CreateTransactionAction $action,
    ) {}
    
    private function authorizeCashier(): void {
        $user = Auth::user();
        abort_unless($user, 401);
        abort_unless($user->hasRole('cashier'), 403);
    }
    
    public function store(StoreTransactionRequest $request): RedirectResponse {
        // Convert request data ke DTOs
        $items = Collection::make($request->input('items'))
            ->map(fn (array $item) => new TransactionItemData(
                procedureId: $item['procedure_id'],
                procedureName: $item['procedure_name'],
            ))->all();
        
        $data = new CreateTransactionData(
            items: $items,
            voucherCode: $request->input('voucher_code'),
        );
        
        // Execute action
        $transaction = $this->action->execute($data);
        
        return redirect()->route('transactions.index');
    }
}
```

**Pattern Digunakan**:
- Dependency Injection via constructor
- Action Pattern untuk business logic
- DTO Pattern untuk data transfer
- Form Request untuk validation

#### **`Http/Requests/`** - Form Request Validation
Validasi input dari form sebelum masuk ke controller/action. FormRequest membuat validation logic terpisah dan reusable.

Struktur:
```
Http/Requests/
└── Transaction/
    └── StoreTransactionRequest.php    # Validasi input create transaction
```

**Contoh**: `StoreTransactionRequest`
```php
class StoreTransactionRequest extends FormRequest {
    public function authorize(): bool {
        // Check authorization
        return $this->user()->hasRole('cashier');
    }
    
    public function rules(): array {
        return [
            'patient_name' => 'required|string|max:255',
            'patient_email' => 'nullable|email',
            'patient_phone' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.procedure_id' => 'required|uuid',
            'items.*.procedure_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
        ];
    }
    
    public function messages(): array {
        return [
            'items.required' => 'Minimal harus ada 1 item transaksi',
            'voucher_code.exists' => 'Kode voucher tidak ditemukan',
        ];
    }
}
```

**Usage di Controller**:
```php
public function store(StoreTransactionRequest $request) {
    // Data sudah tervalidasi di sini
    $validated = $request->validated();
    
    // Lanjutkan proses
}
```

**Benefits**:
- Separated validation logic
- Reusable validation rules
- Authorization check
- Custom error messages

#### **`Livewire/`** - Livewire Components
Interactive reactive components untuk real-time UI updates tanpa page refresh. Digunakan untuk forms kompleks, data tables, dan real-time validasi.

Struktur dan file yang ada:
```
Livewire/
├── Actions/               # Utility actions untuk Livewire (reusable methods)
├── Settings/              # Settings components
├── Transactions/
│   └── CreateTransaction.php      # Form create/edit transaction dengan real-time validation
└── Voucher/
    ├── CreateVoucher.php          # Form create voucher
    ├── EditVoucher.php            # Form edit voucher
    └── ListVouchers.php           # Tabel list vouchers dengan filtering & pagination
```

**Contoh**: `CreateTransaction` component
```php
class CreateTransaction extends Component {
    // Form data
    public string $voucher_code = '';
    public array $items = [];
    public float $total_amount = 0;
    
    // Real-time validation
    #[Validate('required|string')]
    public string $description = '';
    
    public function mount(?Transaction $transaction = null) {
        if ($transaction) {
            $this->fill($transaction->toArray());
        }
    }
    
    public function addItem() {
        $this->items[] = ['product' => '', 'quantity' => 1, 'price' => 0];
    }
    
    #[On('voucher-selected')]
    public function applyVoucher($code) {
        $this->voucher_code = $code;
        // Calculate discount real-time
        $this->calculateDiscount();
    }
    
    public function calculateDiscount() {
        // Calculate total dan discount
    }
    
    public function save() {
        $this->validate();
        // Create/update transaction
        redirect()->route('transactions.index');
    }
    
    public function render() {
        return view('livewire.transactions.create-transaction');
    }
}
```

**Contoh**: `ListVouchers` component
```php
class ListVouchers extends Component {
    #[Validate('nullable|string')]
    public string $search = '';
    
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    
    public function updatedSearch() {
        // Auto-filter saat user mengetik
    }
    
    public function sort($field) {
        // Toggle sort direction
    }
    
    public function delete(Voucher $voucher) {
        $voucher->delete();
    }
    
    #[Computed]
    public function vouchers() {
        return Voucher::query()
            ->when($this->search, fn($q) => $q->where('code', 'like', "%{$this->search}%"))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);
    }
    
    public function render() {
        return view('livewire.voucher.list-vouchers', [
            'vouchers' => $this->vouchers,
        ]);
    }
}
```

**Key Features Livewire yang Digunakan**:
- **Real-time Validation**: `#[Validate()]` attribute
- **Event Handling**: `#[On()]` untuk listen custom events
- **Computed Properties**: `#[Computed]` untuk lazy-loaded data
- **Live Model Binding**: `#[Validate('live')]` untuk validasi on-the-fly
- **Pagination**: Built-in Livewire pagination


#### **`Services/`** - Service Classes
Business logic yang complex, reusable, dan independent dari framework. Services tidak aware tentang request/response.

Struktur dan file yang ada:
```
Services/
├── API/
│   └── RecruitmentApiService.php      # Integration ke recruitment API
├── PDF/                               # PDF generation services
├── Report/                            # Report generation services
├── Telegram/                          # Telegram bot/notification services
└── Voucher/
    ├── VoucherCalculationService.php  # Hitung discount voucher
    ├── VoucherManagementService.php   # CRUD & management voucher
    └── VoucherRetrievalService.php    # Fetch voucher data dengan filters
```

**Contoh**: `VoucherCalculationService`
```php
class VoucherCalculationService {
    public function calculate(Voucher $voucher, float $amount): float {
        // Check if voucher is valid
        if (!$voucher->is_active) {
            throw new InvalidVoucherException('Voucher tidak aktif');
        }
        
        if (!$this->isInValidDateRange($voucher)) {
            throw new InvalidVoucherException('Voucher sudah expired');
        }
        
        // Calculate discount based on type
        return match ($voucher->type) {
            VoucherTypes::PERCENTAGE => $amount * ($voucher->value / 100),
            VoucherTypes::FIXED_AMOUNT => $voucher->value,
            default => 0,
        };
    }
    
    private function isInValidDateRange(Voucher $voucher): bool {
        return now()->between($voucher->start_date, $voucher->end_date);
    }
}
```

**Contoh**: `VoucherManagementService`
```php
class VoucherManagementService {
    public function create(VoucherData $data): Voucher {
        return Voucher::create($data->toArray());
    }
    
    public function update(Voucher $voucher, VoucherData $data): Voucher {
        $voucher->update($data->toArray());
        return $voucher;
    }
    
    public function deactivate(Voucher $voucher): void {
        $voucher->update(['is_active' => false]);
        event(new VoucherDeactivated($voucher));
    }
}
```

**Benefit Service Pattern**:
- Decoupled dari Controller/Livewire
- Reusable di multiple places (Controller, Jobs, Commands)
- Easy to test
- Centralized business logic

#### **`Repositories/`** - Repository Pattern
Abstraksi akses data untuk decoupling dari database layer. Memudahkan switch database driver di masa depan.

Struktur:
```
Repositories/
├── Contracts/
│   └── VoucherRepositoryInterface.php    # Interface/contract
└── Eloquent/
    └── VoucherRepository.php             # Implementasi Eloquent
```

**Contoh Interface**:
```php
interface VoucherRepositoryInterface {
    public function getActive(): Collection;
    public function findByCode(string $code): ?Voucher;
    public function create(array $data): Voucher;
    public function update(Voucher $voucher, array $data): Voucher;
    public function delete(Voucher $voucher): void;
}
```

**Contoh Implementasi Eloquent**:
```php
class VoucherRepository implements VoucherRepositoryInterface {
    public function getActive(): Collection {
        return Voucher::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();
    }
    
    public function findByCode(string $code): ?Voucher {
        return Voucher::where('code', $code)->first();
    }
    
    public function create(array $data): Voucher {
        return Voucher::create($data);
    }
    
    public function update(Voucher $voucher, array $data): Voucher {
        $voucher->update($data);
        return $voucher;
    }
    
    public function delete(Voucher $voucher): void {
        $voucher->delete();
    }
}
```

**Usage di Service/Controller**:
```php
class VoucherController {
    public function __construct(
        private VoucherRepositoryInterface $repository
    ) {}
    
    public function index() {
        return $this->repository->getActive();
    }
}
```

**Benefit**:
- Easy to mock untuk testing
- Switch database tanpa ubah business logic
- Centralized query logic
- Single source of truth untuk database queries

#### **`DTOs/`** - Data Transfer Objects
Plain objects untuk transfer data antar layers tanpa Eloquent overhead. DTOs memastikan type-safety dan make code lebih predictable.

DTOs yang ada:
```
DTOs/
├── CreateTransactionData.php          # DTO untuk create transaction form
├── UpdateTransactionData.php          # DTO untuk update transaction
├── TransactionItemData.php            # DTO untuk transaction item detail
├── VoucherCalculationResultData.php   # DTO untuk hasil kalkulasi voucher
├── VoucherData.php                    # DTO untuk voucher
└── Dashboard/
    └── DashboardSummaryData.php       # DTO untuk dashboard summary
```

**Contoh**: `CreateTransactionData`
```php
readonly class CreateTransactionData {
    public function __construct(
        public string $patientName,
        public ?string $patientEmail,
        public ?string $patientPhone,
        public ?string $patientGender,
        public ?string $patientDob,
        public ?string $insuranceId,
        public array $items,  // TransactionItemData[]
        public string $cashierId,
    ) {}
}
```

**Contoh**: `TransactionItemData`
```php
readonly class TransactionItemData {
    public function __construct(
        public string $procedureId,
        public string $procedureName,
        public int $quantity = 1,
        public float $price,
    ) {}
}
```

**Usage Pattern** di Controller:
```php
class TransactionController {
    public function store(StoreTransactionRequest $request): RedirectResponse {
        // Convert dari request ke DTOs
        $items = Collection::make($request->input('items'))
            ->map(fn (array $item) => new TransactionItemData(
                procedureId: $item['procedure_id'],
                procedureName: $item['procedure_name'],
                quantity: $item['quantity'],
                price: $item['price'],
            ))->all();
        
        $data = new CreateTransactionData(
            patientName: $request->string('patient_name'),
            patientEmail: $request->string('patient_email'),
            items: $items,
            cashierId: Auth::id(),
        );
        
        // Pass ke Action
        $transaction = $this->action->execute($data);
        
        return redirect()->route('transactions.index');
    }
}
```

**Benefits DTOs**:
- Type-safe data transfer
- Single responsibility - fokus pada data shape
- Easy to test
- Documentation via properties

#### **`Events/`** - Application Events
Decouple logic menggunakan event system. Events di-dispatch ketika terjadi action tertentu, dan listeners menangani business logic terkait.

Events yang ada:
```
Events/
└── Voucher/
    ├── VoucherCreated.php   # Dispatched ketika voucher dibuat
    ├── VoucherUpdated.php   # Dispatched ketika voucher diupdate
    └── VoucherDeleted.php   # Dispatched ketika voucher dihapus
```

**Contoh**: `VoucherCreated` event
```php
class VoucherCreated {
    public function __construct(
        public Voucher $voucher,
    ) {}
}

// Dispatch di CreateVoucherAction
event(new VoucherCreated($voucher));
```

**Contoh**: Listener untuk event
```php
class SendVoucherNotification {
    public function handle(VoucherCreated $event): void {
        // Send notification ke admin
        Notification::send($admins, new NewVoucherNotification($event->voucher));
    }
}
```

**Register Listener** di `app/Providers/EventServiceProvider.php`:
```php
protected $listen = [
    VoucherCreated::class => [
        SendVoucherNotification::class,
    ],
];
```

#### **`Jobs/`** - Queued Jobs
Untuk background jobs yang dijalankan asynchronously. Saat ini folder kosong, tapi bisa digunakan untuk:
- Mengirim email asynchronously
- Generate report besar
- Proses data bulk
- Integrasi external API

**Contoh job** (jika ada):
```php
class SendDailyReportEmail implements ShouldQueue {
    public function handle() {
        $report = Transaction::generateDailyReport();
        Mail::send(new DailyReportMail($report));
    }
}

// Dispatch
SendDailyReportEmail::dispatch();
```

**Note**: Project saat ini menggunakan database queue connection untuk compatibility

#### **`Support/`** - Helper & Support Classes
Utility classes, traits, dan helper functions.

Struktur:
```
Support/
├── Concerns/
│   ├── PasswordValidationRules.php     # Reusable password validation rules
│   └── ProfileValidationRules.php      # Reusable profile validation rules
├── Helpers/                            # Helper functions (kosong - bisa digunakan)
└── Responses/                          # Custom response classes (kosong - bisa digunakan)
```

**Contoh**: `PasswordValidationRules` concern
```php
class PasswordValidationRules {
    public static function rules(): array {
        return [
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',  // lowercase, uppercase, digit
                'confirmed',
            ],
        ];
    }
    
    public static function messages(): array {
        return [
            'password.regex' => 'Password harus memiliki huruf besar, huruf kecil, dan angka',
        ];
    }
}

// Usage di FormRequest
use Support\Concerns\PasswordValidationRules;

class UpdatePasswordRequest extends FormRequest {
    public function rules(): array {
        return PasswordValidationRules::rules();
    }
    
    public function messages(): array {
        return PasswordValidationRules::messages();
    }
}
```

#### **`Enums/`** - Enumeration Classes
Type-safe enums untuk konstanta. Enums memastikan hanya value yang valid yang bisa digunakan.

Enums yang ada:
- `TransactionStatus` - Status transaksi (Draft, Paid, Cancelled)
- `VoucherTypes` - Tipe voucher (Percentage, Fixed Amount)

**Contoh**: `TransactionStatus` enum
```php
enum TransactionStatus: string {
    case Draft = 'draft';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}
```

**Usage**:
```php
// Set status
$transaction->status = TransactionStatus::Draft;

// Check status
if ($transaction->status === TransactionStatus::Paid) {
    // Transaction sudah dibayar
}

// Get all cases
foreach (TransactionStatus::cases() as $status) {
    echo $status->value;  // 'draft', 'paid', 'cancelled'
}
```

**Database Casting**:
```php
class Transactions extends Model {
    protected function casts(): array {
        return [
            'status' => TransactionStatus::class,  // Auto cast
        ];
    }
}

// Auto casting
$transaction = Transactions::find(1);
$transaction->status;  // Returns TransactionStatus enum, not string
```

**Blade Usage**:
```blade
@if($transaction->status === \App\Enums\TransactionStatus::Paid)
    <span class="badge badge-success">Paid</span>
@endif
```

**Benefits**:
- Type-safe (IDE autocomplete)
- Impossible state prevention
- Easy refactoring
- Self-documenting code

#### **`Mail/`** - Mailable Classes
Email templates dan logic untuk mengirim email. Mailables define email content, recipients, dan attachments.

Contoh:
- `DailyTransactionReportMail` - Template laporan transaksi harian

**Contoh**: `DailyTransactionReportMail`
```php
class DailyTransactionReportMail extends Mailable {
    use Queueable;
    
    public function __construct(
        private array $reportData,
    ) {}
    
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Daily Transaction Report - ' . now()->format('Y-m-d'),
        );
    }
    
    public function content(): Content {
        return new Content(
            view: 'emails.daily-report',
            with: $this->reportData,
        );
    }
    
    public function attachments(): array {
        return [];
    }
}
```

**Usage**:
```php
// Queue email untuk dikirim asynchronously
Mail::queue(new DailyTransactionReportMail($data));

// Send immediately
Mail::send(new DailyTransactionReportMail($data));
```

#### **`Policies/`** - Authorization Policies
Define who can do what pada resource tertentu. Policies membuat authorization logic terpisah dari controller.

Policies yang ada:
- `TransactionPolicy` - Authorization untuk Transactions model
- `VoucherPolicy` - Authorization untuk Voucher model

**Contoh**: `TransactionPolicy`
```php
class TransactionPolicy {
    public function view(User $user, Transactions $transactions): bool {
        // Hanya cashier bisa view transaction
        return $user->hasRole('cashier');
    }
    
    public function create(User $user): bool {
        return $user->hasRole('cashier');
    }
    
    public function update(User $user, Transactions $transactions): bool {
        // Hanya bisa update jika belum dibayar
        return $user->hasRole('cashier') && 
               is_null($transactions->paid_at) &&
               $transactions->status !== TransactionStatus::Paid;
    }
    
    public function delete(User $user, Transactions $transactions): bool {
        // Hanya bisa delete jika belum dibayar
        return $user->hasRole('cashier') && is_null($transactions->paid_at);
    }
}
```

**Contoh**: `VoucherPolicy`
```php
class VoucherPolicy {
    public function create(User $user): bool {
        return $user->hasRole('marketing');
    }
    
    public function update(User $user, Voucher $voucher): bool {
        return $user->hasRole('marketing');
    }
    
    public function delete(User $user, Voucher $voucher): bool {
        return $user->hasRole('marketing');
    }
}
```

**Usage di Controller**:
```php
class TransactionController {
    public function update(Request $request, Transactions $transaction) {
        // Automatically checks policy
        $this->authorize('update', $transaction);
        
        // If unauthorized, throws 403 Forbidden
    }
}
```

**Usage di Blade**:
```blade
@can('update', $transaction)
    <button>Edit Transaction</button>
@endcan
```

**Register Policies** di `app/Providers/AuthServiceProvider.php`:
```php
protected $policies = [
    Transactions::class => TransactionPolicy::class,
    Voucher::class => VoucherPolicy::class,
];
```

#### **`Providers/`** - Service Providers
Bootstrap aplikasi dan register services, bindings, events, dan configurations.

Providers yang ada:
- `AppServiceProvider` - Register aplikasi services dan logic
- `FortifyServiceProvider` - Configure Laravel Fortify

**Contoh**: `AppServiceProvider`
```php
class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        // Bind interfaces ke implementations
        $this->app->bind(VoucherRepositoryInterface::class, VoucherRepository::class);
    }
    
    public function boot(): void {
        // Boot/configure services
        
        // Register Fortify views & responses
        // Register event listeners
        // Register macros
    }
}
```

**Contoh**: `FortifyServiceProvider`
```php
class FortifyServiceProvider extends ServiceProvider {
    public function register(): void {
        //
    }
    
    public function boot(): void {
        // Configure Fortify features
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();
            
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }
        });
        
        // Customize login view
        Fortify::loginView(fn () => view('auth.login'));
    }
}
```

**Register Providers** di `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
];
```

#### **`Exceptions/`** - Custom Exceptions
Handle errors dengan graceful error handling. Berbagai exception untuk berbagai kasus.

Struktur:
```
Exceptions/
├── API/                          # API exceptions (404, 422, etc)
├── Business/
│   └── TransactionPaidException.php    # Error ketika transaction sudah dibayar
└── Voucher/
    └── InvalidVoucherException.php     # Error ketika voucher invalid
```

**Contoh**: `InvalidVoucherException`
```php
class InvalidVoucherException extends Exception {
    public function __construct(string $message = 'Voucher tidak valid') {
        parent::__construct($message);
    }
}

// Usage
throw new InvalidVoucherException('Voucher sudah expired');
```

**Contoh**: `TransactionPaidException`
```php
class TransactionPaidException extends Exception {
    public function __construct(string $message = 'Transaksi sudah dibayar') {
        parent::__construct($message);
    }
}

// Usage
if ($transaction->is_paid) {
    throw new TransactionPaidException();
}
```

**Exception Handler** di `app/Exceptions/Handler.php`:
```php
public function render($request, Throwable $exception) {
    if ($exception instanceof InvalidVoucherException) {
        return back()->withError($exception->getMessage());
    }
    
    return parent::render($request, $exception);
}
```

#### **`Console/Commands/`** - Artisan Commands
Custom console commands untuk task automation dan background operations.

Commands yang ada:
- `DeactivateExpiredVouchersCommand` - Otomatis deactivate voucher yang expired
- `SendDailytransactionReport` - Kirim laporan transaksi harian

**Contoh**: `DeactivateExpiredVouchersCommand`
```php
class DeactivateExpiredVouchersCommand extends Command {
    protected $signature = 'voucher:deactivate-expired';
    protected $description = 'Deactivate expired vouchers';
    
    public function handle(): int {
        $expired = Voucher::where('end_date', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
        
        $this->info("Deactivated {$expired} vouchers");
        return 0;
    }
}
```

**Contoh**: `SendDailytransactionReport`
```php
class SendDailytransactionReport extends Command {
    protected $signature = 'report:daily-transactions';
    protected $description = 'Send daily transaction report';
    
    public function handle(): int {
        $report = Transaction::generateDailyReport();
        Mail::send(new DailyTransactionReportMail($report));
        
        $this->info('Daily report sent');
        return 0;
    }
}
```

**Usage**:
```bash
# Run command
php artisan voucher:deactivate-expired
php artisan report:daily-transactions

# List all commands
php artisan list

# Get command help
php artisan help voucher:deactivate-expired
```

**Scheduling** (`routes/console.php`):
```php
Schedule::command('report:daily-transactions')
    ->dailyAt('01:00');  // Run setiap hari jam 1 pagi

Schedule::command('voucher:deactivate-expired')
    ->dailyAt('00:00');  // Run setiap hari jam 12 malam
```

**Run Scheduler**:
```bash
# Tambahkan ke crontab untuk production
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

#### **`Rules/`** - Custom Validation Rules
Custom validation rules yang dapat digunakan di multiple Form Requests.

Struktur:
```
Rules/
└── Voucher/
    └── VoucherValidationRules.php    # Custom rules untuk validasi voucher
```

**Contoh**: Custom Voucher Validation Rule
```php
class VoucherValidationRules {
    public static function createRules(): array {
        return [
            'code' => [
                'required',
                'string',
                'unique:vouchers,code',
                new ValidVoucherCode(),  // Custom rule
            ],
            'value' => 'required|numeric|min:0',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
        ];
    }
}

// Usage di StoreVoucherRequest
public function rules(): array {
    return VoucherValidationRules::createRules();
}
```

**Custom Rule Example**:
```php
class ValidVoucherCode implements Rule {
    public function validate($attribute, $value, $fail) {
        // Validate format: V-YYYY-XXX
        if (!preg_match('/^V-\d{4}-[A-Z0-9]{3}$/', $value)) {
            $fail('Format kode voucher tidak sesuai (V-YYYY-XXX)');
        }
    }
}

// Usage di Form Request
'code' => ['required', new ValidVoucherCode()],
```

### `/routes` - URL Routes

**`web.php`** - Web routes
Mendefinisikan semua URL endpoints dan memetakan ke controllers/components.

Middleware groups:
```php
// Public routes
Route::get('/', WelcomeController::class);

// Protected routes (auth required)
Route::middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/transactions', CreateTransaction::class);
});
```

**`console.php`** - Console routes
Mendefinisikan Artisan commands.

**`settings.php`** - Settings routes
Routes untuk user settings dan profile (Fortify).

### `/database` - Database

**`migrations/`** - Database Migrations
File untuk membuat dan memodifikasi database schema.

Run migration:
```bash
php artisan migrate
```

**`factories/`** - Model Factories
Generate dummy data untuk testing.

**`seeders/`** - Database Seeders
Populate database dengan initial data.

Run seeder:
```bash
php artisan db:seed
```

### `/config` - Configuration Files

**`auth.php`** - Authentication configuration
- Guards, providers, password reset

**`app.php`** - Application configuration
- App name, timezone, locale

**`database.php`** - Database configuration
- Connection settings

**`permission.php`** - Spatie Permission configuration
- Role & permission models, tables

**`fortify.php`** - Laravel Fortify configuration
- Features (2FA, email verification, etc)

**`mail.php`** - Email configuration
- SMTP settings, from address

**`queue.php`** - Queue configuration
- Queue connection driver

**`rsd.php`** - Custom RS Unknown configuration
- Business logic config

### `/resources` - Frontend Assets

**`views/`** - Blade templates
- Layout files
- Page views
- Livewire component views (`.blade.php` files di `resources/views/livewire/`)

**`css/`** - CSS files
- Tailwind CSS customizations
- Global styles

**`js/`** - JavaScript files
- Alpine.js scripts
- Frontend logic

### `/tests` - Automated Tests

**`Feature/`** - Feature tests
Test full features dengan database (integration tests).

**`Unit/`** - Unit tests
Test individual units in isolation.

Run tests:
```bash
./vendor/bin/pest
```

### `/bootstrap` - Bootstrap Files

**`app.php`** - Create application instance
**`providers.php`** - Load service providers
**`cache/`** - Bootstrap cache directory

### `/storage` - File Storage

**`app/`** - Application files storage
**`logs/`** - Application logs
**`debugbar/`** - Laravel Debugbar cache

### `/public` - Publicly Accessible Files

**`index.php`** - Entry point untuk web requests
**`robots.txt`** - SEO robots file
**Static assets** - CSS, JS, images yang sudah dicompile

## 6. Request Lifecycle

```
1. User Request
    ↓
2. web.php Routes Match
    ↓
3. Route Middleware Process
    ├── Authenticate (auth guard check)
    ├── SetUserTimezone (set timezone dari user profile)
    └── Role Check (role:cashier, role:marketing)
    ↓
4. Handler Execution (Controller / Livewire Component)
    ├── FormRequest Validation (StoreTransactionRequest)
    ├── Policy Authorization (TransactionPolicy::update)
    └── Handler Logic
    ↓
5. Business Logic Layer
    ├── Action Pattern (CreateTransactionAction)
    └── Service Layer (VoucherCalculationService)
    ↓
6. Database Operations
    ├── Model Operations (Transactions::create)
    └── Activity Log (auto-tracked oleh LogsActivity trait)
    ↓
7. Event Dispatch (jika ada event listeners)
    ↓
8. Response
    ├── Redirect (dengan session flash)
    ├── View Rendering
    └── JSON Response
```

**Contoh Flow untuk Create Transaction**:
```
POST /transactions
    ↓
Route middleware: auth, setTimezone, role:cashier
    ↓
TransactionController::store(StoreTransactionRequest $request)
    ↓
FormRequest validation
    ↓
CreateTransactionAction::execute(CreateTransactionData $data)
    ↓
VoucherCalculationService::calculate()
    ↓
Transactions::create() + TransactionItem::create()
    ↓
Activity logged automatically
    ↓
Redirect to transactions.index with success message
```

## 7. Database Schema Overview

### Main Tables

**users**
- id (UUID primary key)
- name
- email (unique)
- email_verified_at
- password
- remember_token
- two_factor_secret
- two_factor_recovery_codes
- timezone (user's preferred timezone)
- created_at, updated_at

**transactions**
- id (UUID primary key)
- invoice_number (unique)
- patient_name
- patient_email (nullable)
- patient_phone (nullable)
- patient_gender (enum: male, female, other)
- patient_dob (nullable, date)
- insurance_id (nullable)
- insurance_name (nullable)
- subtotal (decimal)
- discount_total (decimal)
- grand_total (decimal)
- paid_at (nullable, timestamp)
- status (string: draft, paid, cancelled)
- cashier_id (UUID, foreign key to users)
- created_at, updated_at

**transaction_items**
- id (UUID primary key)
- transaction_id (UUID, foreign key to transactions)
- procedure_id (string)
- procedure_name (string)
- base_price (decimal)
- voucher_type (nullable: percentage, fixed)
- voucher_value (nullable, decimal)
- discount_amount (decimal)
- final_price (decimal)
- created_at, updated_at

**vouchers**
- id (UUID primary key)
- insurance_id (string)
- insurance_name (string)
- type (enum: percentage, fixed)
- value (decimal)
- max_discount (nullable, decimal)
- start_date (date)
- end_date (date)
- is_active (boolean)
- created_by (UUID, nullable, foreign key to users)
- updated_by (UUID, nullable, foreign key to users)
- created_at, updated_at
- deleted_at (soft delete)

**model_has_roles** (Spatie Permission)
- model_id (UUID)
- role_id
- model_type

**model_has_permissions** (Spatie Permission)
- permission_id
- model_id (UUID)
- model_type

**role_has_permissions** (Spatie Permission)
- permission_id
- role_id

**roles** (Spatie Permission)
- id
- name (admin, marketing, cashier)
- guard_name

**permissions** (Spatie Permission)
- id
- name
- guard_name

**activity_log** (Spatie Activity Log)
- id
- log_name (transactions, vouchers, etc)
- description (created, updated, deleted)
- subject_type (model class)
- subject_id (UUID)
- causer_type (User class)
- causer_id (UUID)
- properties (JSON dengan perubahan data)
- created_at

### Relationships

```
User
  ├── hasMany(Transactions) via cashier_id
  ├── hasMany(Activity) via causer_id
  └── hasRoles (Spatie)

Transactions
  ├── belongsTo(User) as cashier
  └── hasMany(TransactionItem)

TransactionItem
  └── belongsTo(Transactions)

Voucher
  ├── belongsTo(User) as createdBy
  └── belongsTo(User) as updatedBy
```

## 8. Best Practices & Patterns

### 1. Action Pattern
Gunakan Actions untuk reusable business logic:
```php
class CreateTransactionAction {
    public function execute(CreateTransactionData $data): Transaction {
        // Complex logic here
        return $transaction;
    }
}
```

### 2. DTO Pattern
Transfer data antar layers tanpa Eloquent:
```php
$data = CreateTransactionData::from($request->validated());
$transaction = $this->action->execute($data);
```

### 3. Service Pattern
Untuk business logic yang complex:
```php
$calculator = new VoucherCalculationService();
$discount = $calculator->calculate($voucher, $amount);
```

### 4. Repository Pattern
Abstraksi database queries:
```php
$repository = new TransactionRepository();
$transactions = $repository->getByUser($userId);
```

### 5. Event-Driven
Decouple logic dengan events:
```php
event(new TransactionCompleted($transaction));
```

## 9. Common Workflows

### Create Transaction

Flow:
```
User Input (Livewire CreateTransaction Component)
    ↓
Real-time Validation (Livewire #[Validate])
    ↓
Form Submit → HTTP Request
    ↓
StoreTransactionRequest Validation
    ↓
TransactionController::store()
    ↓
Convert to DTOs (CreateTransactionData + TransactionItemData[])
    ↓
CreateTransactionAction::execute()
    ↓
Hitung discount (VoucherCalculationService)
    ↓
Create Transaction + TransactionItems (Models)
    ↓
Event Dispatch (VoucherCreated jika buat voucher baru)
    ↓
Activity Log (Transactions model LogsActivity)
    ↓
Redirect to Success Page
```

**Code Flow**:
```php
// Livewire Component
public function save() {
    $this->validate();  // Real-time validation
    
    $data = new CreateTransactionData(
        patientName: $this->patient_name,
        items: $this->items,
        // ...
    );
    
    $transaction = $this->action->execute($data);
}

// Controller
public function store(StoreTransactionRequest $request) {
    $items = Collection::make($request->input('items'))
        ->map(fn (array $item) => new TransactionItemData(...))
        ->all();
    
    $data = new CreateTransactionData(...);
    $transaction = $this->action->execute($data);
    
    return redirect()->route('transactions.index');
}

// Action
public function execute(CreateTransactionData $data): Transactions {
    // Calculate voucher discount
    $discount = $this->voucherService->calculate($voucher, $total);
    
    // Create transaction
    $transaction = Transactions::create([
        'subtotal' => $total,
        'discount_total' => $discount,
        'grand_total' => $total - $discount,
        'status' => TransactionStatus::Draft,
    ]);
    
    // Create items
    foreach ($data->items as $item) {
        $transaction->items()->create($item->toArray());
    }
    
    // Activity auto-logged by model
    return $transaction;
}
```

### Voucher Calculation

Flow:
```
VoucherCalculationService::calculate($voucher, $amount)
    ↓
Validate voucher status (is_active)
    ↓
Check date range (start_date ≤ now() ≤ end_date)
    ↓
Throw exception jika invalid
    ↓
Match type (Percentage atau Fixed Amount)
    ↓
Calculate discount amount
    ↓
Return discount value
```

**Code**:
```php
class VoucherCalculationService {
    public function calculate(Voucher $voucher, float $amount): float {
        if (!$voucher->is_active) {
            throw new InvalidVoucherException('Voucher tidak aktif');
        }
        
        if (!$this->isInValidDateRange($voucher)) {
            throw new InvalidVoucherException('Voucher sudah expired');
        }
        
        return match ($voucher->type) {
            VoucherTypes::Percentage => $amount * ($voucher->value / 100),
            VoucherTypes::FixedAmount => $voucher->value,
        };
    }
}
```

### Role-Based Access

Flow:
```
HTTP Request
    ↓
Route Middleware: auth, role:cashier
    ↓
Middleware Check: User authenticated?
    ↓
Middleware Check: User has role 'cashier'?
    ↓
Route Handler (Controller/Livewire)
    ↓
Policy Check (TransactionPolicy)
    ↓
If Authorized → Execute
    ↓
If Not Authorized → 403 Forbidden
```

**Middleware Level** (`routes/web.php`):
```php
Route::middleware(['auth', 'role:cashier'])
    ->group(function () {
        // Only cashier users can access
    });
```

**Policy Level** (Controller):
```php
// Check policy
$this->authorize('update', $transaction);

// Or abort if not authorized
abort_unless(
    $user->hasRole('cashier') && $user->can('update', $transaction),
    403
);
```

**View Level** (Blade):
```blade
@can('update', $transaction)
    <button>Edit</button>
@endcan

@role('cashier')
    <div>Cashier-only content</div>
@endrole
```

---

**Last Updated**: May 2026
