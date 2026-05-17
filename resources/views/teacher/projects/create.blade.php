@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pages/teacher-project-create.css') }}">
@endpush

@section('content')
<div class="wrap">

    <div class="card">

        <div class="header">
            ➕ Create New Project
        </div>

        <div class="body">

            {{-- ERRORS --}}
            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('teacher.projects.store') }}"
                enctype="multipart/form-data"
            >
                @csrf

                {{-- TITLE --}}
                <div class="field">
                    <label>Project Title</label>

                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                    >
                </div>

                {{-- DESCRIPTION --}}
                <div class="field">
                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="5"
                        required
                    >{{ old('description') }}</textarea>
                </div>

                {{-- INSTRUCTION FILE / LINK --}}
                <div class="field">

                    <label>Instruction File <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>

                    {{-- TAB SWITCHER --}}
                    <div style="display:flex;gap:0;margin-bottom:.75rem;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;width:fit-content;">
                        <button type="button" id="tab-file-create"
                                onclick="switchTab('create','file')"
                                style="padding:.5rem 1.1rem;font-size:.82rem;font-weight:600;border:none;cursor:pointer;background:#4f46e5;color:white;">
                            📎 Upload File
                        </button>
                        <button type="button" id="tab-link-create"
                                onclick="switchTab('create','link')"
                                style="padding:.5rem 1.1rem;font-size:.82rem;font-weight:600;border:none;cursor:pointer;background:white;color:#6b7280;">
                            🔗 Paste Link
                        </button>
                    </div>

                    {{-- FILE UPLOAD --}}
                    <div id="panel-file-create">
                        <div class="file-box">
                            <input type="file" name="instruction_file">
                            <div class="file-help">
                                Upload PDF, DOCX, PPT, ZIP, Images or any project instructions. Maximum file size: 20MB
                            </div>
                        </div>
                    </div>

                    {{-- LINK INPUT --}}
                    <div id="panel-link-create" style="display:none;">
                        <input type="url"
                               name="instruction_link"
                               placeholder="https://drive.google.com/... or any URL"
                               value="{{ old('instruction_link') }}"
                               style="width:100%;padding:.75rem 1rem;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;box-sizing:border-box;">
                        <div class="file-help" style="margin-top:6px;">
                            Paste a Google Drive, Dropbox, OneDrive link, or any URL.
                        </div>
                    </div>

                </div>

                {{-- MAX SCORE --}}
                <div class="field">
                    <label>Max Score</label>

                    <input
                        type="number"
                        name="max_score"
                        min="1"
                        max="1000"
                        value="{{ old('max_score') }}"
                        required
                    >
                </div>

                {{-- ASSIGNMENT MODE --}}
                <div class="field">
                    <label>Assignment Type</label>
                    <div style="display:flex;gap:0;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;width:fit-content;margin-bottom:1rem;">
                        <button type="button" id="tab-group"
                                onclick="switchAssignMode('group')"
                                style="padding:.5rem 1.2rem;font-size:.85rem;font-weight:600;border:none;cursor:pointer;background:#4f46e5;color:white;">
                            👥 Assign to Group
                        </button>
                        <button type="button" id="tab-individual"
                                onclick="switchAssignMode('individual')"
                                style="padding:.5rem 1.2rem;font-size:.85rem;font-weight:600;border:none;cursor:pointer;background:white;color:#6b7280;">
                            🧑 Assign to Students
                        </button>
                    </div>

                    {{-- ══════════════════════════════
                         GROUP PANEL
                    ══════════════════════════════ --}}
                    <div id="panel-group">

                        {{-- Section filter for groups --}}
                        @if($sections->isNotEmpty())
                        <div style="margin-bottom:.65rem;">
                            <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                                🏷️ Filter by Section
                            </label>
                            <div style="position:relative;">
                                <select id="groupSectionFilter"
                                    onchange="filterGroupsBySection(this.value)"
                                    style="width:100%;padding:.5rem 2rem .5rem .8rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;background:#fff;appearance:none;cursor:pointer;">
                                    <option value="">— All sections —</option>
                                    @foreach($sections as $sec)
                                        <option value="{{ $sec->id }}">{{ $sec->name }}{{ $sec->school_year ? " · " . $sec->school_year : "" }}{{ $sec->semester ? " · " . $sec->semester : "" }}</option>
                                    @endforeach
                                </select>
                                <span style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">▾</span>
                            </div>
                        </div>
                        @endif

                        <select name="group_id" id="groupSelect">
                            <option value="">-- Select Group (optional) --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}"
                                    data-section="{{ $group->section_id ?? '' }}"
                                    {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}{{ $group->section ? " · " . $group->section->name : "" }}
                                </option>
                            @endforeach
                        </select>
                        <div id="groupNoMatch" style="display:none;padding:.6rem .8rem;background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;font-size:.78rem;color:#92400e;margin-top:.4rem;">
                            No groups found for this section.
                        </div>
                    </div>

                    {{-- ══════════════════════════════
                         INDIVIDUAL STUDENTS PANEL
                    ══════════════════════════════ --}}
                    <div id="panel-individual" style="display:none;">
                        @if($myStudents->isEmpty())
                            <div style="padding:1rem;background:#f9fafb;border:1px dashed #d1d5db;border-radius:10px;color:#6b7280;font-size:.85rem;">
                                No students registered under your code yet. Students must enter your teacher code first.
                            </div>
                        @else
                            {{-- Build section→students map for JS --}}
                            @php
                                $secStudentMap = [];
                                foreach($sections as $sec) {
                                    $secStudentMap[$sec->id] = $sec->students->pluck('id')->toArray();
                                }
                            @endphp

                            {{-- Section filter for students --}}
                            @if($sections->isNotEmpty())
                            <div style="margin-bottom:.65rem;">
                                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                                    🏷️ Filter by Section
                                </label>
                                <div style="position:relative;">
                                    <select id="studentSectionFilter"
                                        onchange="filterStudentsBySection(this.value)"
                                        style="width:100%;padding:.5rem 2rem .5rem .8rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;background:#fff;appearance:none;cursor:pointer;">
                                        <option value="">— All sections (show everyone) —</option>
                                        @foreach($sections as $sec)
                                            <option value="{{ $sec->id }}">{{ $sec->name }}{{ $sec->school_year ? " · " . $sec->school_year : "" }}{{ $sec->semester ? " · " . $sec->semester : "" }}</option>
                                        @endforeach
                                    </select>
                                    <span style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">▾</span>
                                </div>
                            </div>
                            @endif

                            {{-- Search box --}}
                            <input type="text" id="studentSearchProj"
                                placeholder="🔍 Search by name or ID..."
                                oninput="filterStudentsProj()"
                                style="width:100%;padding:.5rem .85rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;box-sizing:border-box;margin-bottom:.5rem;">

                            <div style="padding:.75rem 1rem;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;max-height:260px;overflow-y:auto;" id="studentCheckList">
                                <div style="font-size:.78rem;color:#6b7280;margin-bottom:.5rem;">Select students to assign to this project:</div>
                                @foreach($myStudents as $st)
                                    @php
                                        $studentSecs = [];
                                        foreach($secStudentMap as $secId => $stuIds) {
                                            if(in_array($st->id, $stuIds)) $studentSecs[] = $secId;
                                        }
                                    @endphp
                                    <label class="proj-student-row"
                                           data-name="{{ strtolower($st->name) }}"
                                           data-sid="{{ strtolower($st->student_id ?? '') }}"
                                           data-uid="{{ $st->id }}"
                                           data-sections="{{ implode(',', $studentSecs) }}"
                                           style="display:flex;align-items:center;gap:.6rem;padding:.45rem .2rem;cursor:pointer;border-bottom:1px solid #f3f4f6;">
                                        <input type="checkbox"
                                               name="student_ids[]"
                                               value="{{ $st->id }}"
                                               {{ in_array($st->id, old('student_ids', [])) ? 'checked' : '' }}
                                               style="width:16px;height:16px;accent-color:#4f46e5;flex-shrink:0;">
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:.87rem;display:flex;align-items:center;gap:.35rem;flex-wrap:wrap;">
                                                {{ $st->name }}
                                                @if(!empty($studentSecs))
                                                    @foreach($sections->whereIn('id', $studentSecs) as $ss)
                                                        <span style="padding:.1rem .4rem;background:#eef2ff;color:#4f46e5;border-radius:999px;font-size:.65rem;font-weight:700;">{{ $ss->name }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if($st->student_id)
                                                <div style="font-size:.72rem;color:#9ca3af;">🆔 {{ $st->student_id }} · {{ $st->department ?? 'No course' }}</div>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                                <div id="projNoMatch" style="display:none;padding:1rem;text-align:center;color:#9ca3af;font-size:.82rem;">No students match.</div>
                            </div>
                            <div style="margin-top:.5rem;font-size:.75rem;color:#6b7280;">
                                Each selected student will receive this project individually and be graded separately.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- START DATE --}}
                <div class="field">

                    <label>Start Date</label>

                    <input
                        type="datetime-local"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        required
                    >
                </div>

                {{-- DUE DATE --}}
                <div class="field">

                    <label>Due Date</label>

                    <input
                        type="datetime-local"
                        name="due_date"
                        value="{{ old('due_date') }}"
                        required
                    >
                </div>

                {{-- STATUS --}}
                <div class="field">

                    <label>Status</label>

                    <select
                        name="status"
                        required
                    >
                        <option
                            value="draft"
                            {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}
                        >
                            Draft
                        </option>

                        <option
                            value="published"
                            {{ old('status') == 'published' ? 'selected' : '' }}
                        >
                            Published
                        </option>

                        <option
                            value="ongoing"
                            {{ old('status') == 'ongoing' ? 'selected' : '' }}
                        >
                            Ongoing
                        </option>

                        <option
                            value="completed"
                            {{ old('status') == 'completed' ? 'selected' : '' }}
                        >
                            Completed
                        </option>

                    </select>
                </div>

                {{-- TASKS SECTION --}}
                <hr style="margin:2rem 0;border:none;border-top:1px solid #e5e7eb;">

                <div class="field">

                    <label style="font-size:1rem;font-weight:700;">
                        📋 Assign Tasks
                        <span style="color:#9ca3af;font-size:.78rem;font-weight:400;margin-left:.4rem;">(optional)</span>
                    </label>

                    {{-- Task list: starts empty --}}
                    <div id="task-wrapper"></div>

                    {{-- Empty state shown when no tasks added --}}
                    <div id="task-empty-state" style="padding:1.25rem;background:#f9fafb;border:1.5px dashed #e5e7eb;border-radius:10px;text-align:center;color:#9ca3af;font-size:.85rem;margin-bottom:.75rem;">
                        No tasks added yet. Click <strong>+ Add Task</strong> below to create one.
                    </div>

                    {{-- ADD TASK BUTTON --}}
                    <button
                        type="button"
                        class="btn btn-add"
                        id="add-task-btn"
                    >
                        ➕ Add Task
                    </button>

                </div>

                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    🚀 Create Project
                </button>

            </form>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/js/pages/teacher-project-create.js') }}"></script>
<script>
function switchAssignMode(mode) {
    var groupPanel      = document.getElementById('panel-group');
    var individualPanel = document.getElementById('panel-individual');
    var groupBtn        = document.getElementById('tab-group');
    var individualBtn   = document.getElementById('tab-individual');

    if (mode === 'group') {
        groupPanel.style.display      = 'block';
        individualPanel.style.display = 'none';
        groupBtn.style.background     = '#4f46e5';
        groupBtn.style.color          = 'white';
        individualBtn.style.background = 'white';
        individualBtn.style.color      = '#6b7280';
        document.querySelectorAll('input[name="student_ids[]"]').forEach(function(cb){ cb.checked = false; });
    } else {
        groupPanel.style.display      = 'none';
        individualPanel.style.display = 'block';
        individualBtn.style.background = '#4f46e5';
        individualBtn.style.color      = 'white';
        groupBtn.style.background      = 'white';
        groupBtn.style.color           = '#6b7280';
        var gs = document.getElementById('groupSelect');
        if (gs) gs.value = '';
    }
}

// ── Filter groups by section ──────────────────────────────────────────
function filterGroupsBySection(sectionId) {
    var opts    = document.querySelectorAll('#groupSelect option');
    var visible = 0;
    opts.forEach(function(opt) {
        if (!opt.value) { opt.style.display = ''; return; } // keep placeholder
        var show = !sectionId || opt.dataset.section == sectionId;
        opt.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    // If the currently selected option is now hidden, reset
    var gs = document.getElementById('groupSelect');
    if (gs && gs.selectedIndex > 0) {
        var sel = gs.options[gs.selectedIndex];
        if (sel && sel.style.display === 'none') gs.value = '';
    }
    var noMatch = document.getElementById('groupNoMatch');
    if (noMatch) noMatch.style.display = (sectionId && visible === 0) ? 'block' : 'none';
}

// ── Filter individual students by section + search ────────────────────
var _projSectionFilter = '';

function filterStudentsBySection(sectionId) {
    _projSectionFilter = sectionId;
    filterStudentsProj();
}

function filterStudentsProj() {
    var q   = (document.getElementById('studentSearchProj') || {}).value;
    q = q ? q.toLowerCase().trim() : '';
    var rows = document.querySelectorAll('.proj-student-row');
    var vis  = 0;
    rows.forEach(function(row) {
        var secs    = row.dataset.sections || '';
        var secMatch = !_projSectionFilter || secs.split(',').indexOf(_projSectionFilter) > -1;
        var qMatch   = !q || row.dataset.name.indexOf(q) > -1 || row.dataset.sid.indexOf(q) > -1;
        var show = secMatch && qMatch;
        row.style.display = show ? '' : 'none';
        if (show) vis++;
    });
    var nm = document.getElementById('projNoMatch');
    if (nm) nm.style.display = vis === 0 ? 'block' : 'none';
}

// Restore mode on validation error
@if(old('student_ids'))
    switchAssignMode('individual');
@endif
</script>
@endsection
