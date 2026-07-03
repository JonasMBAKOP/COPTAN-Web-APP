@extends('layouts.app')
@section('title', 'Message aux Parents')
@section('page-title', 'Nouveau Message aux Parents')
@section('page-subtitle', 'SMS et/ou WhatsApp')

@section('content')

<div class="max-w-2xl" x-data="parentMsgForm()">
    <form method="POST" action="{{ route('communication.parents.store') }}">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
            <p class="text-sm font-bold text-red-700 mb-2">Le formulaire contient des erreurs :</p>
            <ul class="text-sm text-red-600 list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 space-y-4">
            <h3 class="text-sm font-black mb-2" style="color:#1A3A6B;">Destinataires</h3>

            <div class="grid sm:grid-cols-3 gap-3">
                @foreach([
                    'all'=>['Tous les parents','👥'],
                    'class'=>['Une classe','🏫'],
                    'selected'=>['Élèves spécifiques','🎯'],
                ] as $val => [$label,$icon])
                <label class="flex flex-col items-center gap-2 p-4 rounded-xl border-2
                               cursor-pointer transition-all"
                       :class="targetType==='{{ $val }}' ? 'border-blue-400 bg-blue-50' : 'border-gray-200'">
                    <input type="radio" name="target_type" value="{{ $val }}"
                           x-model="targetType" class="hidden">
                    <span class="text-2xl">{{ $icon }}</span>
                    <span class="text-sm font-bold text-gray-700">{{ $label }}</span>
                </label>
                @endforeach
            </div>

            <div x-show="targetType === 'class'">
                <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                <select name="class_group_id" class="w-full px-3 py-2.5 border border-gray-200
                                                        rounded-xl text-sm focus:outline-none bg-white">
                    <option value="">— Choisir —</option>
                    @foreach($classes->groupBy('level.section.name') as $sec => $cls)
                    <optgroup label="{{ $sec }}">
                        @foreach($cls as $c)
                        <option value="{{ $c->id }}" {{ old('class_group_id') == $c->id ? 'selected' : '' }}>{{ $c->full_name }}</option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>

            <div x-show="targetType === 'selected'">
                <label class="block text-sm font-medium text-gray-700 mb-2">Élèves spécifiques</label>
                <p class="text-xs text-gray-400 mb-3">Sélectionnez des élèves précis pour un envoi ciblé.</p>
                @if($students->isEmpty())
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-500">
                    Aucun élève actif trouvé pour l'année scolaire active.
                </div>
                @else
                @php $selectedStudents = old('student_ids', []); @endphp
                <select name="student_ids[]" multiple size="10"
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none bg-white">
                    @foreach($students->groupBy(fn($student) => $student->enrollments->first()?->classGroup?->full_name ?? 'Sans classe') as $className => $grouped)
                    <optgroup label="{{ $className }}">
                        @foreach($grouped as $student)
                        <option value="{{ $student->id }}"
                            {{ in_array($student->id, (array) $selectedStudents) ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->matricule }})
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-2">Maintenez Ctrl / Cmd pour sélectionner plusieurs élèves.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 space-y-4">
            <h3 class="text-sm font-black mb-2" style="color:#1A3A6B;">Canal d'envoi</h3>
            <div class="grid grid-cols-3 gap-3">
                @foreach(['sms'=>'SMS','whatsapp'=>'WhatsApp','both'=>'Les deux'] as $val=>$lbl)
                <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2
                               cursor-pointer text-sm font-bold transition-all"
                       :class="channel==='{{ $val }}' ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-600'">
                    <input type="radio" name="channel" value="{{ $val }}" x-model="channel" class="hidden">
                    {{ $lbl }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5 space-y-4">
            <h3 class="text-sm font-black mb-2" style="color:#1A3A6B;">Message</h3>
            <input type="text" name="subject" placeholder="Sujet (optionnel)"
                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none">
            <textarea name="body" rows="5" maxlength="1000" x-model="body"
                      placeholder="Votre message..."
                      class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm
                             focus:outline-none resize-none"></textarea>
            @error('body')
            <p class="text-xs text-red-600 mt-2">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-400 text-right" x-text="body.length + '/1000 caractères'"></p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('communication.parents.index') }}"
               class="px-6 py-3 border border-gray-200 rounded-xl text-sm font-medium
                      text-gray-600 hover:bg-gray-50">Annuler</a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl text-white text-sm font-bold hover:shadow-md"
                    style="background-color:#1A5C2A;">
                Envoyer le message
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function parentMsgForm() {
    return {
        targetType: @json(old('target_type', 'all')),
        channel: @json(old('channel', 'both')),
        body: @json(old('body', '')),
    };
}
</script>
@endpush