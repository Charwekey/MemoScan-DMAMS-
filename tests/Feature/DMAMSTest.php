<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Memo;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DMAMSTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->staff = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        $this->viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password123'),
            'role' => 'viewer',
        ]);
    }

    /**
     * Test login view.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('DMAMS');
    }

    /**
     * Test authentication flow.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test role permissions on dashboard.
     */
    public function test_dashboard_can_be_rendered_for_authenticated_users(): void
    {
        $response = $this->actingAs($this->viewer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('System Dashboard');
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test memo archive routing.
     */
    public function test_memos_archive_accessible_to_all_authenticated_users(): void
    {
        $response = $this->actingAs($this->viewer)->get('/memos');
        $response->assertStatus(200);
    }

    /**
     * Test staff can access create form, but viewer cannot.
     */
    public function test_staff_can_access_memo_upload_screen(): void
    {
        $response = $this->actingAs($this->staff)->get('/memos/create');
        $response->assertStatus(200);
    }

    public function test_viewer_cannot_access_memo_upload_screen(): void
    {
        $response = $this->actingAs($this->viewer)->get('/memos/create');
        $response->assertStatus(403);
    }

    /**
     * Test memo store.
     */
    public function test_staff_can_upload_memo_file(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('memo_doc.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->staff)->post('/memos', [
            'subject' => 'Staff Test Memo',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
            'description' => 'Test memo description',
            'file_upload' => $file,
        ]);

        $response->assertRedirect('/memos');
        
        $this->assertDatabaseHas('memos', [
            'subject' => 'Staff Test Memo',
            'from_department' => 'Registry',
            'category' => 'Academic',
        ]);

        $memo = Memo::where('subject', 'Staff Test Memo')->first();
        Storage::disk('local')->assertExists($memo->file_path);

        // Assert audit log was recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'upload_memo',
            'user_id' => $this->staff->id,
            'target_id' => $memo->id,
        ]);
    }

    /**
     * Test camera base64 scanning upload.
     */
    public function test_uploading_camera_scanned_base64_pdf(): void
    {
        Storage::fake('local');

        // Simulating the base64 output of a PDF file
        // Base64 encoding of "%PDF-1.4 ... %%EOF"
        $base64Pdf = 'data:application/pdf;base64,JVBERi0xLjQKMSAwIG9iagogIDw8IC9UeXBlIC9DYXRhbG9nCiAgICAgL1BhZ2VzIDIgMCBSCiAgPj4KZW5kb2JqCjIgMCBvYmoKICA8PCAvVHlwZSAvUGFnZXMKICAgICAvS2lkcyBbIDMgMCBSIF0KICAgICAvQ291bnQgMQogID4+CmVuZG9iagozIDAgb2JqCiAgPDwgL1R5cGUgL1BhZ2UKICAgICAvUGFyZW50IDIgMCBSCiAgICAgL01lZGlhQm94IFsgMCAwIDU5NSA4NDIgXQogID4+CmVuZG9iagp4cmVmCjAgNAowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA2NiAwMDAwMCBuIAowMDAwMDAwMTIzIDAwMDAwIG4gCnRyYWlsZXIKICA8PCAvU2l6ZSA0CiAgICAgL1Jvb3QgMSAwIFIKICA+PgpzdGFydHhyZWYKMjEwCiUlRU9GCg==';

        $response = $this->actingAs($this->staff)->post('/memos', [
            'subject' => 'Scanned Document Memo',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
            'description' => 'Camera scan description',
            'scanned_file' => $base64Pdf,
        ]);

        $response->assertRedirect('/memos');
        $this->assertDatabaseHas('memos', [
            'subject' => 'Scanned Document Memo',
        ]);

        $memo = Memo::where('subject', 'Scanned Document Memo')->first();
        Storage::disk('local')->assertExists($memo->file_path);
    }

    /**
     * Test camera base64 scanning upload with filename parameter (from jsPDF output).
     */
    public function test_uploading_camera_scanned_base64_pdf_with_filename_prefix(): void
    {
        Storage::fake('local');

        // Simulating the base64 output of a PDF file generated by jsPDF (which contains a filename parameter)
        // Base64 encoding of "%PDF-1.4 ... %%EOF" with filename parameter
        $base64Pdf = 'data:application/pdf;filename=generated.pdf;base64,JVBERi0xLjQKMSAwIG9iagogIDw8IC9UeXBlIC9DYXRhbG9nCiAgICAgL1BhZ2VzIDIgMCBSCiAgPj4KZW5kb2JqCjIgMCBvYmoKICA8PCAvVHlwZSAvUGFnZXMKICAgICAvS2lkcyBbIDMgMCBSIF0KICAgICAvQ291bnQgMQogID4+CmVuZG9iagozIDAgb2JqCiAgPDwgL1R5cGUgL1BhZ2UKICAgICAvUGFyZW50IDIgMCBSCiAgICAgL01lZGlhQm94IFsgMCAwIDU5NSA4NDIgXQogID4+CmVuZG9iagp4cmVmCjAgNAowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA2NiAwMDAwMCBuIAowMDAwMDAwMTIzIDAwMDAwIG4gCnRyYWlsZXIKICA8PCAvU2l6ZSA0CiAgICAgL1Jvb3QgMSAwIFIKICA+PgpzdGFydHhyZWYKMjEwCiUlRU9GCg==';

        $response = $this->actingAs($this->staff)->post('/memos', [
            'subject' => 'Scanned Document Memo With Filename',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
            'description' => 'Camera scan description',
            'scanned_file' => $base64Pdf,
        ]);

        $response->assertRedirect('/memos');
        $this->assertDatabaseHas('memos', [
            'subject' => 'Scanned Document Memo With Filename',
        ]);

        $memo = Memo::where('subject', 'Scanned Document Memo With Filename')->first();
        Storage::disk('local')->assertExists($memo->file_path);
    }

    /**
     * Test admin can delete user but not themselves.
     */
    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'New Staff Member',
            'email' => 'new.staff@test.com',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name' => 'New Staff Member',
            'role' => 'staff',
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertRedirect('/users');
        $response->assertSessionHas('error');
        
        // Admin user is still in the database
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_other_user(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->staff->id}");
        $response->assertRedirect('/users');
        
        $this->assertDatabaseMissing('users', ['id' => $this->staff->id]);
    }

    public function test_staff_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->staff)->get('/users');
        $response->assertStatus(403);
    }

    /**
     * Test memo edit limits for staff.
     */
    public function test_staff_can_edit_own_uploaded_memo(): void
    {
        $memo = Memo::create([
            'subject' => 'Staff Owned Memo',
            'memo_number' => 'REG/2026/002',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
            'file_path' => 'memos/dummy.pdf',
            'uploaded_by' => $this->staff->id,
        ]);

        $response = $this->actingAs($this->staff)->get("/memos/{$memo->id}/edit");
        $response->assertStatus(200);

        $response = $this->actingAs($this->staff)->put("/memos/{$memo->id}", [
            'subject' => 'Staff Owned Memo Updated',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
        ]);

        $response->assertRedirect('/memos');
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'subject' => 'Staff Owned Memo Updated',
        ]);
    }

    public function test_staff_cannot_edit_others_uploaded_memo(): void
    {
        $otherStaff = User::create([
            'name' => 'Other Staff',
            'email' => 'other@test.com',
            'password' => bcrypt('password123'),
            'role' => 'staff',
        ]);

        $memo = Memo::create([
            'subject' => 'Other Owned Memo',
            'memo_number' => 'REG/2026/003',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
            'file_path' => 'memos/dummy.pdf',
            'uploaded_by' => $otherStaff->id,
        ]);

        $response = $this->actingAs($this->staff)->get("/memos/{$memo->id}/edit");
        $response->assertStatus(403);

        $response = $this->actingAs($this->staff)->put("/memos/{$memo->id}", [
            'subject' => 'Hack Attempt',
            'from_department' => 'Registry',
            'to_department' => 'Academic Affairs',
            'memo_date' => '2026-05-21',
            'category' => 'Academic',
        ]);

        $response->assertStatus(403);
    }
}
