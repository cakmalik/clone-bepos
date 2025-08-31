<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SalesControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
{
    parent::setUp();
   $user = User::create([
        'role_id' => 1,
        'users_name' => 'superadmin',
        'username' => 'superadmin',
        'email' => 'superadmin@mail.com',
        'password' => Hash::make('123'),
        'pin' => Crypt::encryptString('123456')
    ]);

    // Masuk sebagai pengguna ini untuk pengujian
    $this->actingAs($user);
}

    public function testCreateSales()
    {
        // Data request untuk membuat penjualan
        $data = [
            'sales_id' => null,
            'outlet_id' => 1,
            'user_id' => 1,
            'cashier_machine_id' => 1,
            'customer_id' => null,
            'nominal_amount' => 40000,
            'discount_amount' => 50,
            'discount_type' => 'percentage',
            'nominal_pay' => 0,
            'table_id' => 1,
            'payment_method_id' => 1,
            'status' => 'draft',
            'sales_details' => [
                [
                    'product_id' => 1,
                    'qty' => 2,
                ],
                [
                    'product_id' => 2,
                    'qty' => 2,
                ],
            ],
        ];

        // Kirim permintaan POST ke endpoint 'store'
        $response = $this->json('POST', '/api/v2/transactions/sales', $data);

        // Periksa apakah penjualan berhasil dibuat
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Data berhasil disimpan',
            // Tambahkan asert lainnya sesuai kebutuhan
        ]);

        // Anda juga dapat menambahkan lebih banyak asert untuk memeriksa data yang dikembalikan

        // Contoh: Memeriksa apakah penjualan memiliki ID yang valid
        $responseData = $response->json('data');
        $this->assertNotNull($responseData['sale_id']);

        // Anda dapat menambahkan lebih banyak asert sesuai kebutuhan

        // Setelah test, Anda dapat membersihkan data yang dibuat jika perlu
        // Misalnya, menghapus user, outlet, dan metode pembayaran yang telah dibuat.
    }
}
