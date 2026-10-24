<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'q'      => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'in:INSERT,UPDATE,DELETE'],
        ]);

        $query = DB::table('audit_logs as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.changed_by')
            ->select([
                'a.id', 'a.table_name', 'a.record_id', 'a.action',
                'a.old_values', 'a.new_values', 'a.created_at',
                'u.name as by_name',
            ])
            ->orderByDesc('a.id');

        if ($request->filled('action')) {
            $query->where('a.action', $request->input('action'));
        }

        if ($request->filled('q')) {
            $q = trim($request->input('q'));

            if (ctype_digit($q)) {
                $query->where('a.record_id', (int) $q);
            } else {
                $like = '%' . addcslashes($q, '%_\\') . '%';
                $query->where(function ($w) use ($like) {
                    $w->where('a.new_values', 'like', $like)
                      ->orWhere('a.old_values', 'like', $like);
                });
            }
        }

        return response()->json($query->simplePaginate(25));
    }
}