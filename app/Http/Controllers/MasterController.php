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
use App\Models\WhatsappTemplate;
use App\Models\ActivityLog;

class MasterController extends Controller
{
    /* ============================================================
     |  Small helper — write an activity log if the table exists
     ============================================================ */
    private function log(string $module, string $type, string $desc): void
    {
        try {
            ActivityLog::create([
                'user_id'       => auth()->id(),
                'module'        => $module,
                'activity_type' => $type,
                'description'   => $desc,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // logging must never break the main action
        }
    }

    /* ============================================================
     |  MAIN PAGE
     ============================================================ */
    public function index()
    {
        $categories = ServiceCategory::with(['domains' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        $warranties        = Warranty::orderBy('name')->get();
        $priorities        = Priority::orderBy('display_order')->get();
        $slaMatrix         = SlaMatrix::with('priority')->orderBy('priority_id')->get();
        $templates         = WhatsappTemplate::orderBy('template_name')->get();

        $counts = [
            'service'   => $categories->count(),
            'expense'   => $expenseCategories->count(),
            'priority'  => $priorities->count(),
            'warranty'  => $warranties->count(),
            'sla'       => $slaMatrix->count(),
            'templates' => $templates->count(),
        ];

        return view('master_data', compact(
            'categories', 'expenseCategories','warranties', 'priorities',
            'slaMatrix', 'templates', 'counts'
        ));
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
        $this->log('Service Category', 'Create', "Created category: {$category->category_name}");

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
        $this->log('Service Category', 'Update', "Updated category: {$category->category_name}");

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
        $this->log('Service Category', 'Delete', "Deleted category: {$name}");

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
        $this->log('Service Domain', 'Create', "Created domain: {$domain->domain_name}");

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
        $this->log('Service Domain', 'Update', "Updated domain: {$domain->domain_name}");

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
        $this->log('Service Domain', 'Delete', "Deleted domain: {$name}");

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
        $this->log('Expense Category', 'Create', "Created expense: {$expense->name}");

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
    $this->log('Warranty Category', 'Create', "Created warranty: {$warranty->name}");

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
    $this->log('Warranty Category', 'Update', "Updated warranty: {$warranty->name}");

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
        $this->log('Expense Category', 'Update', "Updated expense: {$expense->name}");

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
        $this->log('Expense Category', 'Delete', "Deleted expense: {$name}");

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
        $this->log('Warranty', 'Delete', "Deleted expense: {$name}");

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
        $this->log('Priority', 'Create', "Created priority: {$priority->name}");

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
        $this->log('Priority', 'Update', "Updated priority: {$priority->name}");

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
        $this->log('Priority', 'Delete', "Deleted priority: {$name}");

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
    public function storeSlaMatrix(Request $request)
    {
        $request->validate([
            'priority_id'      => 'required|exists:priorities,id',
            'response_time'    => 'required|integer|min:1',
            'assignment_time'  => 'required|integer|min:1',
            'resolution_time'  => 'required|integer|min:1',
            'alert_percentage' => 'required|numeric|min:1|max:100',
            'status'           => 'nullable|in:0,1',
        ]);

        if (SlaMatrix::where('priority_id', $request->priority_id)->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'SLA already exists for this priority.',
            ], 422);
        }

        $sla = SlaMatrix::create([
            'priority_id'      => $request->priority_id,
            'response_time'    => $request->response_time,
            'assignment_time'  => $request->assignment_time,
            'resolution_time'  => $request->resolution_time,
            'alert_percentage' => $request->alert_percentage,
            'status'           => $request->status ?? 1,
        ]);
        $this->log('SLA Matrix', 'Create', 'Created SLA row.');

        return response()->json([
            'status'  => true,
            'message' => 'SLA Matrix created successfully.',
            'data'    => $sla,
        ]);
    }

    public function updateSlaMatrix(Request $request, $id)
    {
        $sla = SlaMatrix::findOrFail($id);

        $request->validate([
            'priority_id'      => 'required|exists:priorities,id',
            'response_time'    => 'required|integer|min:1',
            'assignment_time'  => 'required|integer|min:1',
            'resolution_time'  => 'required|integer|min:1',
            'alert_percentage' => 'required|numeric|min:1|max:100',
            'status'           => 'nullable|in:0,1',
        ]);

        $dupe = SlaMatrix::where('priority_id', $request->priority_id)
            ->where('id', '!=', $id)->exists();

        if ($dupe) {
            return response()->json([
                'status'  => false,
                'message' => 'SLA already exists for this priority.',
            ], 422);
        }

        $sla->update([
            'priority_id'      => $request->priority_id,
            'response_time'    => $request->response_time,
            'assignment_time'  => $request->assignment_time,
            'resolution_time'  => $request->resolution_time,
            'alert_percentage' => $request->alert_percentage,
            'status'           => $request->status ?? 1,
        ]);
        $this->log('SLA Matrix', 'Update', 'Updated SLA row.');

        return response()->json([
            'status'  => true,
            'message' => 'SLA Matrix updated successfully.',
            'data'    => $sla,
        ]);
    }

    public function deleteSlaMatrix($id)
    {
        $sla = SlaMatrix::findOrFail($id);
        $sla->delete();
        $this->log('SLA Matrix', 'Delete', 'Deleted SLA row.');

        return response()->json([
            'status'  => true,
            'message' => 'SLA Matrix deleted successfully.',
        ]);
    }

    /**
     * Save the whole SLA table at once (the "Save Changes" button).
     * Expects: rows = [ {priority_id, response_time, assignment_time,
     *                    resolution_time, alert_percentage}, ... ]
     */
    public function saveAllSla(Request $request)
    {
        $request->validate([
            'rows'                    => 'required|array|min:1',
            'rows.*.priority_id'      => 'required|exists:priorities,id',
            'rows.*.response_time'    => 'required|integer|min:1',
            'rows.*.assignment_time'  => 'required|integer|min:1',
            'rows.*.resolution_time'  => 'required|integer|min:1',
            'rows.*.alert_percentage' => 'required|numeric|min:1|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->rows as $row) {
                SlaMatrix::updateOrCreate(
                    ['priority_id' => $row['priority_id']],
                    [
                        'response_time'    => $row['response_time'],
                        'assignment_time'  => $row['assignment_time'],
                        'resolution_time'  => $row['resolution_time'],
                        'alert_percentage' => $row['alert_percentage'],
                        'status'           => $row['status'] ?? 1,
                    ]
                );
            }
            DB::commit();
            $this->log('SLA Matrix', 'Update', 'Saved full SLA matrix.');

            return response()->json(['status' => true, 'message' => 'SLA Matrix saved successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
     |  WHATSAPP TEMPLATES
     ============================================================ */
    public function storeWhatsappTemplate(Request $request)
    {
        $data = $request->validate([
            'template_name' => 'required|string|max:255',
            'trigger_event' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:500',
            'body'          => 'required|string',
            'variables'     => 'nullable|array',
            'status'        => 'nullable|in:0,1',
        ]);

        $data['status'] = $data['status'] ?? 1;
        $template = WhatsappTemplate::create($data);
        $this->log('WhatsApp Template', 'Create', "Created template: {$template->template_name}");

        return response()->json([
            'status'  => true,
            'message' => 'WhatsApp Template created successfully.',
            'data'    => $template,
        ]);
    }

    public function updateWhatsappTemplate(Request $request, $id)
    {
        $template = WhatsappTemplate::findOrFail($id);

        $data = $request->validate([
            'template_name' => 'sometimes|required|string|max:255',
            'trigger_event' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:500',
            'body'          => 'required|string',
            'variables'     => 'nullable|array',
            'status'        => 'nullable|in:0,1',
        ]);

        $template->update($data);
        $this->log('WhatsApp Template', 'Update', "Updated template: {$template->template_name}");

        return response()->json([
            'status'  => true,
            'message' => 'WhatsApp Template updated successfully.',
            'data'    => $template,
        ]);
    }

    public function deleteWhatsappTemplate($id)
    {
        $template = WhatsappTemplate::findOrFail($id);
        $name = $template->template_name;
        $template->delete();
        $this->log('WhatsApp Template', 'Delete', "Deleted template: {$name}");

        return response()->json([
            'status'  => true,
            'message' => 'WhatsApp Template deleted successfully.',
        ]);
    }

    public function changeWhatsappTemplateStatus(Request $request, $id)
    {
        $template = WhatsappTemplate::findOrFail($id);
        $template->status = $request->has('status')
            ? (int) $request->status
            : ($template->status ? 0 : 1);
        $template->save();

        return response()->json([
            'status'         => true,
            'message'        => 'Template status updated.',
            'current_status' => $template->status,
        ]);
    }

    /* ============================================================
     |  ACTIVITY LOGS
     ============================================================ */
    public function activityLogs()
    {
        $logs = ActivityLog::orderByDesc('created_at')->paginate(30);
        return view('activity_logs', compact('logs'));
    }

    public function viewActivityLog($id)
    {
        $log = ActivityLog::findOrFail($id);
        return response()->json(['status' => true, 'data' => $log]);
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
