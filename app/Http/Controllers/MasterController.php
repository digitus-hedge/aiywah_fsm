<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\ServiceCategory;
use App\Models\ServiceDomain;
use App\Models\ExpenseCategory;
use App\Models\Warranty;
use App\Models\Priority;
use App\Models\SlaMatrix;
use App\Models\AlertType;
use App\Models\UserAlertPermission;
use App\Models\User;
use App\Models\UserAlertSchedule;
use Illuminate\Support\Facades\Storage;
use App\Models\Client;
use App\Models\PdfTemplate;

class MasterController extends Controller
{


    /* ============================================================
     |  MAIN PAGE
     ============================================================ */
    public function index()
    {
        $categories = ServiceCategory::with(['domains' => fn($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        $warranties        = Warranty::orderBy('name')->get();
        $priorities        = Priority::orderBy('display_order')->get();
        $slaMatrix         = SlaMatrix::with('priority')->orderBy('priority_id')->get();

        $pdfTemplates = PdfTemplate::latest()->get();

        $counts = [
            'service'   => $categories->count(),
            'expense'   => $expenseCategories->count(),
            'priority'  => $priorities->count(),
            'warranty'  => $warranties->count(),
            'sla'       => $slaMatrix->count(),
                'template'  => $pdfTemplates->count(),
        ];

        return view('master_data', compact(
            'categories',
            'expenseCategories',
            'warranties',
            'priorities',
            'slaMatrix',
            'counts',
             'pdfTemplates'
        ));
    }
    


    /* ============================================================
 |  PDF TEMPLATES (one per company)
 ============================================================ */
private const PDF_IMAGE_FIELDS = ['header_image', 'letterhead_image', 'footer_image'];

private function pdfTemplateRules($id = null): array
{
    $img = 'nullable|image|mimes:jpg,jpeg,png|max:2048';

    return [
        'template_name'    => 'required|string|max:150|unique:pdf_templates,template_name,' . ($id ?? 'NULL') . ',id',
        'header_image'     => $img,
        'letterhead_image' => $img,
        'footer_image'     => $img,
        'status'           => 'required|in:0,1',
    ];
}

private function pdfTemplateMessages(): array
{
    return ['template_name.unique' => 'A template with this name already exists. Use a different name.'];
}

public function storePdfTemplate(Request $request)
{
    $data = $request->validate($this->pdfTemplateRules(), $this->pdfTemplateMessages());

    $hasAny = false;
    foreach (self::PDF_IMAGE_FIELDS as $field) {
        if ($request->hasFile($field)) {
            $data[$field] = $request->file($field)->store('pdf-templates', 'public');
            $hasAny = true;
        }
    }

    if (! $hasAny) {
        return response()->json(['status' => false, 'message' => 'Upload at least one image.'], 422);
    }

    $template = PdfTemplate::create($data);

    return response()->json([
        'status'  => true,
        'message' => 'PDF template created successfully.',
        'data'    => $template,
    ]);
}

public function updatePdfTemplate(Request $request, $id)
{
    $template = PdfTemplate::findOrFail($id);
    $data     = $request->validate($this->pdfTemplateRules($id), $this->pdfTemplateMessages());

    foreach (self::PDF_IMAGE_FIELDS as $field) {
        if ($request->hasFile($field)) {
            if ($template->{$field}) {
                Storage::disk('public')->delete($template->{$field});
            }
            $data[$field] = $request->file($field)->store('pdf-templates', 'public');
        } else {
            unset($data[$field]);   // keep the image that is already saved
        }
    }

    $template->update($data);

    return response()->json([
        'status'  => true,
        'message' => 'PDF template updated successfully.',
        'data'    => $template,
    ]);
}

public function deletePdfTemplate($id)
{
    $template = PdfTemplate::findOrFail($id);

    foreach (self::PDF_IMAGE_FIELDS as $field) {
        if ($template->{$field}) {
            Storage::disk('public')->delete($template->{$field});
        }
    }
    $template->delete();

    return response()->json(['status' => true, 'message' => 'PDF template deleted successfully.']);
}

    /* ============================================================
     |  SERVICE CATEGORIES
     ============================================================ */
    public function storeServiceCategory(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:service_categories,category_name,NULL,id,deleted_at,NULL',
            'description'   => 'nullable|string|max:500',
            'color_code'    => 'nullable|string|max:20',
            'icon'          => 'nullable|string|max:100',
            'sort_order'    => 'nullable|integer',
            'status'        => 'required|in:0,1',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_by'] = auth()->id();

        $category = ServiceCategory::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Service Category created successfully.',
            'data'    => $category,
        ]);
    }

    public function updateServiceCategory(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $data = $request->validate([
            'category_name' => 'required|string|max:255|unique:service_categories,category_name,' . $id . ',id,deleted_at,NULL',
            'description'   => 'nullable|string|max:500',
            'color_code'    => 'nullable|string|max:20',
            'icon'          => 'nullable|string|max:100',
            'sort_order'    => 'nullable|integer',
            'status'        => 'required|in:0,1',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['updated_by'] = auth()->id();

        $category->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Service Category updated successfully.',
            'data'    => $category,
        ]);
    }

    public function deleteServiceCategory($id)
    {
        $category = ServiceCategory::findOrFail($id);

        if (ServiceDomain::where('service_category_id', $id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'This category cannot be deleted because it contains domains.',
            ], 422);
        }

        $name = $category->category_name;
        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Service Category deleted successfully.',
        ]);
    }

    public function changeServiceCategoryStatus(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->status = $request->has('status')
            ? (int) $request->status
            : ($category->status ? 0 : 1);
        $category->updated_by = auth()->id();
        $category->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Service Category status updated.',
            'current_status' => $category->status,
            'status_text'    => $category->status ? 'Active' : 'Inactive',
        ]);
    }

    /* ============================================================
     |  SERVICE DOMAINS
     ============================================================ */
    public function getServiceDomains($categoryId)
    {
        $domains = ServiceDomain::where('service_category_id', $categoryId)
            ->orderBy('sort_order')
            ->orderBy('domain_name')
            ->get();

        return response()->json(['status' => true, 'data' => $domains]);
    }

    public function storeServiceDomain(Request $request)
    {
        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'domain_name'         => 'required|string|max:255',
            'description'         => 'nullable|string|max:500',
            'sort_order'          => 'nullable|integer',
            'status'              => 'required|in:0,1',
        ]);

        $exists = ServiceDomain::where('service_category_id', $data['service_category_id'])
            ->where('domain_name', $data['domain_name'])
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'This domain already exists under the selected category.',
            ], 422);
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['created_by'] = auth()->id();

        $domain = ServiceDomain::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Service Domain created successfully.',
            'data'    => $domain,
        ]);
    }

    public function updateServiceDomain(Request $request, $id)
    {
        $domain = ServiceDomain::findOrFail($id);

        $data = $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'domain_name'         => 'required|string|max:255',
            'description'         => 'nullable|string|max:500',
            'sort_order'          => 'nullable|integer',
            'status'              => 'required|in:0,1',
        ]);

        $exists = ServiceDomain::where('service_category_id', $data['service_category_id'])
            ->where('domain_name', $data['domain_name'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'This domain already exists under the selected category.',
            ], 422);
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['updated_by'] = auth()->id();

        $domain->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Service Domain updated successfully.',
            'data'    => $domain,
        ]);
    }

    public function destroyServiceDomain($id)
    {
        $domain = ServiceDomain::findOrFail($id);
        $name = $domain->domain_name;
        $domain->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Service Domain deleted successfully.',
        ]);
    }

    public function changeServiceDomainStatus(Request $request, $id)
    {
        $domain = ServiceDomain::findOrFail($id);
        $domain->status = $request->has('status')
            ? (int) $request->status
            : ($domain->status ? 0 : 1);
        $domain->updated_by = auth()->id();
        $domain->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Service Domain status updated.',
            'current_status' => $domain->status,
        ]);
    }

    /* ============================================================
     |  EXPENSE CATEGORIES
     ============================================================ */
    public function storeExpenseCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:expense_categories,name,NULL,id,deleted_at,NULL',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $expense = ExpenseCategory::create($request->only('name', 'description', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Expense Category created successfully.',
            'data'    => $expense,
        ]);
    }


    public function storeWarrantyCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            // 'value'  => 'nullable|string|max:500',
            'value'  => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $warranty = Warranty::create($request->only('name', 'value', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Warranty Category created successfully.',
            'data'    => $warranty,
        ]);
    }



    public function updateWarrantyCategory(Request $request, $id)
    {
        $warranty = Warranty::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'   => 'required|string|max:255',
            // 'value'  => 'nullable|string|max:500',
            'value'  => 'required|integer|min:1',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $warranty->update($request->only('name', 'value', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Warranty Category updated successfully.',
            'data'    => $warranty,
        ]);
    }

    public function updateExpenseCategory(Request $request, $id)
    {
        $expense = ExpenseCategory::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255|unique:expense_categories,name,' . $id . ',id,deleted_at,NULL',
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $expense->update($request->only('name', 'description', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Expense Category updated successfully.',
            'data'    => $expense,
        ]);
    }

    public function deleteExpenseCategory($id)
    {
        $expense = ExpenseCategory::findOrFail($id);
        $name = $expense->name;
        $expense->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Expense Category deleted successfully.',
        ]);
    }



    public function deleteWarrantyCategory($id)
    {
        $warranty = Warranty::findOrFail($id);
        $name = $warranty->name;
        $warranty->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Warranty Category deleted successfully.',
        ]);
    }


    public function changeExpenseCategoryStatus(Request $request, $id)
    {
        $expense = ExpenseCategory::findOrFail($id);
        $expense->status = $request->has('status')
            ? (int) $request->status
            : ($expense->status ? 0 : 1);
        $expense->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Expense Category status updated.',
            'current_status' => $expense->status,
            'status_text'    => $expense->status ? 'Active' : 'Inactive',
        ]);
    }


    public function changeWarrantyCategoryStatus(Request $request, $id)
    {
        $warranty = Warranty::findOrFail($id);
        $warranty->status = $request->has('status')
            ? (int) $request->status
            : ($warranty->status ? 0 : 1);
        $warranty->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Warranty Category status updated.',
            'current_status' => $warranty->status,
            'status_text'    => $warranty->status ? 'Active' : 'Inactive',
        ]);
    }

    /* ============================================================
     |  PRIORITIES
     ============================================================ */
    public function storePriority(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100|unique:priorities,name,NULL,id,deleted_at,NULL',
            'display_order' => 'required|integer|min:1|unique:priorities,display_order,NULL,id,deleted_at,NULL',
            'color'         => 'required|string|max:20',
            'status'        => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $priority = Priority::create($request->only('name', 'display_order', 'color', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Priority created successfully.',
            'data'    => $priority,
        ]);
    }

    public function updatePriority(Request $request, $id)
    {
        $priority = Priority::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:100|unique:priorities,name,' . $id . ',id,deleted_at,NULL',
            'display_order' => 'required|integer|min:1|unique:priorities,display_order,' . $id . ',id,deleted_at,NULL',
            'color'         => 'required|string|max:20',
            'status'        => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $priority->update($request->only('name', 'display_order', 'color', 'status'));

        return response()->json([
            'status'  => true,
            'message' => 'Priority updated successfully.',
            'data'    => $priority,
        ]);
    }

    public function deletePriority($id)
    {
        $priority = Priority::findOrFail($id);

        if (SlaMatrix::where('priority_id', $id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'This priority has an SLA row and cannot be deleted.',
            ], 422);
        }

        $name = $priority->name;
        $priority->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Priority deleted successfully.',
        ]);
    }

    public function changePriorityStatus(Request $request, $id)
    {
        $priority = Priority::findOrFail($id);
        $priority->status = $request->has('status')
            ? (int) $request->status
            : ($priority->status ? 0 : 1);
        $priority->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Priority status updated.',
            'current_status' => $priority->status,
            'status_text'    => $priority->status ? 'Active' : 'Inactive',
        ]);
    }

    /* ============================================================
     |  SLA MATRIX
     ============================================================ */
    /** Shared rules - all times are HOURS now. */
    private function slaRules(string $prefix = ''): array
    {
        return [
            $prefix . 'priority_id'     => 'required|exists:priorities,id',
            $prefix . 'response_time'   => 'required|integer|min:1|max:8760',   // Approve
            $prefix . 'assignment_time' => 'required|integer|min:1|max:8760',   // Dispatch
            $prefix . 'resolution_time' => 'required|integer|min:1|max:8760',   // QC
            $prefix . 'status'          => 'nullable|in:0,1',
        ];
    }

    public function storeSlaMatrix(Request $request)
    {
        $request->validate($this->slaRules());

        if (SlaMatrix::where('priority_id', $request->priority_id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'SLA already exists for this criticality.',
            ], 422);
        }

        $sla = SlaMatrix::create([
            'priority_id'     => $request->priority_id,
            'response_time'   => $request->response_time,
            'assignment_time' => $request->assignment_time,
            'resolution_time' => $request->resolution_time,
            'status'          => $request->status ?? 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'SLA Matrix created successfully.',
            'data'    => $sla,
        ]);
    }

    public function updateSlaMatrix(Request $request, $id)
    {
        $sla = SlaMatrix::findOrFail($id);
        $request->validate($this->slaRules());

        $dupe = SlaMatrix::where('priority_id', $request->priority_id)
            ->where('id', '!=', $id)->exists();

        if ($dupe) {
            return response()->json([
                'status'  => false,
                'message' => 'SLA already exists for this criticality.',
            ], 422);
        }

        $sla->update([
            'priority_id'     => $request->priority_id,
            'response_time'   => $request->response_time,
            'assignment_time' => $request->assignment_time,
            'resolution_time' => $request->resolution_time,
            'status'          => $request->status ?? 1,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'SLA Matrix updated successfully.',
            'data'    => $sla,
        ]);
    }

    public function saveAllSla(Request $request)
    {
        $request->validate(array_merge(
            ['rows' => 'required|array|min:1'],
            $this->slaRules('rows.*.')
        ));

        DB::beginTransaction();
        try {
            foreach ($request->rows as $row) {
                SlaMatrix::updateOrCreate(
                    ['priority_id' => $row['priority_id']],
                    [
                        'response_time'   => $row['response_time'],
                        'assignment_time' => $row['assignment_time'],
                        'resolution_time' => $row['resolution_time'],
                        'status'          => $row['status'] ?? 1,
                    ]
                );
            }
            DB::commit();

            return response()->json(['status' => true, 'message' => 'SLA Matrix saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    // ---- class constant ----
    private const SUMMARY_ALERT_ROLES = [
        ['slug' => 'admin', 'codes' => ['AD', 'SA'], 'label' => 'Admin'],
        ['slug' => 'hop',   'codes' => ['HP'],       'label' => 'HoP'],
        ['slug' => 'se',    'codes' => ['SE'],       'label' => 'Service Engineer'],
        ['slug' => 'ml',    'codes' => ['ML'],       'label' => 'Maintenance Lead'],
        ['slug' => 'ac',    'codes' => ['AC'],       'label' => 'Accounts'],
    ];
 
/* ============================================================
 |  SUMMARY ALERT - MASTER SETTINGS
 |  Select Role -> Select User -> checklist of that role's
 |  Daily Summary alert headings (AlertType), stored per-user
 |  in UserAlertPermission. Everything defaults to checked
 |  (is_enabled = true) until a Super Admin unchecks it here.
 ============================================================ */

    /**
     * GET /masters/summary-alert/users/{roleSlug}
     * List users belonging to the given role slug (admin|hop|se|ml).
     */
    public function summaryAlertUsers(string $roleSlug)
    {
        $role = collect(self::SUMMARY_ALERT_ROLES)->firstWhere('slug', $roleSlug);

        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Unknown role.'], 422);
        }

        $users = User::whereHas('role', fn($q) => $q->whereIn('code', $role['codes']))
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        // Pull enabled state for all these users in one query, not N+1.
        $enabledMap = UserAlertSchedule::whereIn('user_id', $users->pluck('id'))
            ->pluck('is_enabled', 'user_id');

        $users = $users->map(function ($u) use ($enabledMap) {
            $u->summary_enabled = (bool) ($enabledMap[$u->id] ?? false);
            return $u;
        });

        return response()->json(['status' => true, 'data' => $users]);
    }

    /**
     * GET /masters/summary-alert/permissions/{user}
     * The checklist for one user: every AlertType for their role (is_enabled
     * from user_alert_permissions, defaulting to true when no row exists yet)
     * PLUS their send-days schedule (from user_alert_schedules, defaulting to
     * every day true when no row exists yet).
     */
    public function summaryAlertPermissions(int $userId)
    {
        $user = User::with('role')->findOrFail($userId);

        $roleEntry = collect(self::SUMMARY_ALERT_ROLES)
            ->first(fn($r) => in_array($user->role?->code, $r['codes'], true));

        if (!$roleEntry) {
            return response()->json([
                'status'  => false,
                'message' => 'This user\'s role has no Summary Alert items configured.',
            ], 422);
        }

        $alertTypes = AlertType::forRole($roleEntry['slug'])->get();

        $existing = UserAlertPermission::where('user_id', $userId)
            ->pluck('is_enabled', 'alert_type_id');

        $items = $alertTypes->map(function ($type) use ($existing) {
            return [
                'id'          => $type->id,
                'key'         => $type->key,
                'title'       => $type->title,
                'description' => $type->description,
                'is_enabled'  => $existing->has($type->id) ? (bool) $existing[$type->id] : true,
            ];
        })->values();

        $schedule = UserAlertSchedule::where('user_id', $userId)->first();

        $days = [];
        foreach (UserAlertSchedule::DAYS as $day) {
            $days[$day] = $schedule ? (bool) $schedule->{$day} : true; // default: every day
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'items'   => $items,
                'days'    => $days,
                'enabled' => $schedule ? (bool) $schedule->is_enabled : false, // ← NEW: defaults OFF until explicitly turned on
            ],
        ]);
    }

    /**
     * POST /masters/summary-alert/save
     * Body: {
     *   user_id: 12,
     *   items: [{alert_type_id: 3, is_enabled: false}, ...],
     *   days: {monday: true, tuesday: true, ..., sunday: false}
     * }
     */
    public function summaryAlertSave(Request $request)
    {
        $data = $request->validate([
            'user_id'               => 'required|exists:users,id',
            'items'                 => 'required|array|min:1',
            'items.*.alert_type_id' => 'required|exists:alert_types,id',
            'items.*.is_enabled'    => 'required|boolean',
            'days'                  => 'nullable|array',
            'days.monday'           => 'nullable|boolean',
            'days.tuesday'          => 'nullable|boolean',
            'days.wednesday'        => 'nullable|boolean',
            'days.thursday'         => 'nullable|boolean',
            'days.friday'           => 'nullable|boolean',
            'days.saturday'         => 'nullable|boolean',
            'days.sunday'           => 'nullable|boolean',
            'enabled'                => 'required|boolean',   // ← NEW
        ]);

        foreach ($data['items'] as $item) {
            UserAlertPermission::updateOrCreate(
                [
                    'user_id'       => $data['user_id'],
                    'alert_type_id' => $item['alert_type_id'],
                ],
                [
                    'is_enabled' => $item['is_enabled'],
                ]
            );
        }

        $dayValues = [];
        foreach (UserAlertSchedule::DAYS as $day) {
            $dayValues[$day] = $data['days'][$day] ?? true;
        }

        UserAlertSchedule::updateOrCreate(
            ['user_id' => $data['user_id']],
            array_merge($dayValues, ['is_enabled' => $data['enabled']])   // ← NEW
        );

        return response()->json([
            'status'  => true,
            'message' => 'Summary Alert preferences saved.',
        ]);
    }



    /* ============================================================
     |  AJAX READ ENDPOINTS
     ============================================================ */
    public function ajaxCategories()
    {
        return response()->json([
            'status' => true,
            'data'   => ServiceCategory::with('domains')->orderBy('sort_order')->get(),
        ]);
    }

    public function ajaxDomains($categoryId)
    {
        return response()->json([
            'status' => true,
            'data'   => ServiceDomain::where('service_category_id', $categoryId)
                ->orderBy('sort_order')->get(),
        ]);
    }

    public function ajaxExpenses()
    {
        return response()->json([
            'status' => true,
            'data'   => ExpenseCategory::orderBy('name')->get(),
        ]);
    }

    public function ajaxPriorities()
    {
        return response()->json([
            'status' => true,
            'data'   => Priority::orderBy('display_order')->get(),
        ]);
    }

    public function ajaxSla()
    {
        return response()->json([
            'status' => true,
            'data'   => SlaMatrix::with('priority')->orderBy('priority_id')->get(),
        ]);
    }

    public function ajaxTemplates()
    {
        return response()->json([
            'status' => true,
            'data'   => WhatsappTemplate::orderBy('template_name')->get(),
        ]);
    }
}
