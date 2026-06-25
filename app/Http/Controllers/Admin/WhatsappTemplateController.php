<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WhatsappTemplateRequest;
use App\Models\WhatsappTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WhatsappTemplateController extends Controller
{
    public function index(): View
    {
        $tagihan          = WhatsappTemplate::firstOrNew(['type' => 'tagihan']);
        $konfirmasiBayar  = WhatsappTemplate::firstOrNew(['type' => 'konfirmasi_bayar']);

        return view('admin.whatsapp-templates.index', compact('tagihan', 'konfirmasiBayar'));
    }

    public function update(WhatsappTemplateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        WhatsappTemplate::updateOrCreate(
            ['type' => $validated['type']],
            ['template' => $validated['template']],
        );

        return back()->with('success', 'Template pesan WhatsApp berhasil disimpan.');
    }
}
