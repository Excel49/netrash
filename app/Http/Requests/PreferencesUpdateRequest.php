<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferencesUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'theme' => ['nullable', 'in:light,dark,auto'],
            'language' => ['nullable', 'in:id,en'],
            'timezone' => ['nullable', 'in:WIB,WITA,WIT'],
            'notifications.transaction' => ['nullable', 'boolean'],
            'notifications.points' => ['nullable', 'boolean'],
            'notifications.withdrawal' => ['nullable', 'boolean'],
            'notifications.promo' => ['nullable', 'boolean'],
            'notifications.system' => ['nullable', 'boolean'],
            'privacy.public_profile' => ['nullable', 'boolean'],
            'privacy.show_activity' => ['nullable', 'boolean'],
            'privacy.profile_searchable' => ['nullable', 'boolean'],
        ];
    }
    
    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'theme.in' => 'Tema harus light, dark, atau auto',
            'language.in' => 'Bahasa harus Indonesia atau English',
            'timezone.in' => 'Zona waktu harus WIB, WITA, atau WIT',
            'notifications.transaction.boolean' => 'Notifikasi transaksi harus true atau false',
            'notifications.points.boolean' => 'Notifikasi poin harus true atau false',
            'notifications.withdrawal.boolean' => 'Notifikasi penarikan harus true atau false',
            'notifications.promo.boolean' => 'Notifikasi promo harus true atau false',
            'notifications.system.boolean' => 'Notifikasi sistem harus true atau false',
            'privacy.public_profile.boolean' => 'Setting profil publik harus true atau false',
            'privacy.show_activity.boolean' => 'Setting tampilkan aktivitas harus true atau false',
            'privacy.profile_searchable.boolean' => 'Setting pencarian profil harus true atau false',
        ];
    }
}