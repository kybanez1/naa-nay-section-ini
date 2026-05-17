<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/pages/teacher-project-create.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="wrap">

    <div class="card">

        <div class="header">
            ➕ Create New Project
        </div>

        <div class="body">

            
            <?php if($errors->any()): ?>
                <div class="error-box">
                    <ul>
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="<?php echo e(route('teacher.projects.store')); ?>"
                enctype="multipart/form-data"
            >
                <?php echo csrf_field(); ?>

                
                <div class="field">
                    <label>Project Title</label>

                    <input
                        type="text"
                        name="title"
                        value="<?php echo e(old('title')); ?>"
                        required
                    >
                </div>

                
                <div class="field">
                    <label>Description</label>

                    <textarea
                        name="description"
                        rows="5"
                        required
                    ><?php echo e(old('description')); ?></textarea>
                </div>

                
                <div class="field">

                    <label>Instruction File <span style="color:#9ca3af;font-weight:400;">(optional)</span></label>

                    
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

                    
                    <div id="panel-file-create">
                        <div class="file-box">
                            <input type="file" name="instruction_file">
                            <div class="file-help">
                                Upload PDF, DOCX, PPT, ZIP, Images or any project instructions. Maximum file size: 20MB
                            </div>
                        </div>
                    </div>

                    
                    <div id="panel-link-create" style="display:none;">
                        <input type="url"
                               name="instruction_link"
                               placeholder="https://drive.google.com/... or any URL"
                               value="<?php echo e(old('instruction_link')); ?>"
                               style="width:100%;padding:.75rem 1rem;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.9rem;box-sizing:border-box;">
                        <div class="file-help" style="margin-top:6px;">
                            Paste a Google Drive, Dropbox, OneDrive link, or any URL.
                        </div>
                    </div>

                </div>

                
                <div class="field">
                    <label>Max Score</label>

                    <input
                        type="number"
                        name="max_score"
                        min="1"
                        max="1000"
                        value="<?php echo e(old('max_score')); ?>"
                        required
                    >
                </div>

                
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

                    
                    <div id="panel-group">

                        
                        <?php if($sections->isNotEmpty()): ?>
                        <div style="margin-bottom:.65rem;">
                            <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                                🏷️ Filter by Section
                            </label>
                            <div style="position:relative;">
                                <select id="groupSectionFilter"
                                    onchange="filterGroupsBySection(this.value)"
                                    style="width:100%;padding:.5rem 2rem .5rem .8rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;background:#fff;appearance:none;cursor:pointer;">
                                    <option value="">— All sections —</option>
                                    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($sec->id); ?>"><?php echo e($sec->name); ?><?php echo e($sec->school_year ? " · " . $sec->school_year : ""); ?><?php echo e($sec->semester ? " · " . $sec->semester : ""); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <span style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">▾</span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <select name="group_id" id="groupSelect">
                            <option value="">-- Select Group (optional) --</option>
                            <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($group->id); ?>"
                                    data-section="<?php echo e($group->section_id ?? ''); ?>"
                                    <?php echo e(old('group_id') == $group->id ? 'selected' : ''); ?>>
                                    <?php echo e($group->name); ?><?php echo e($group->section ? " · " . $group->section->name : ""); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="groupNoMatch" style="display:none;padding:.6rem .8rem;background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;font-size:.78rem;color:#92400e;margin-top:.4rem;">
                            No groups found for this section.
                        </div>
                    </div>

                    
                    <div id="panel-individual" style="display:none;">
                        <?php if($myStudents->isEmpty()): ?>
                            <div style="padding:1rem;background:#f9fafb;border:1px dashed #d1d5db;border-radius:10px;color:#6b7280;font-size:.85rem;">
                                No students registered under your code yet. Students must enter your teacher code first.
                            </div>
                        <?php else: ?>
                            
                            <?php
                                $secStudentMap = [];
                                foreach($sections as $sec) {
                                    $secStudentMap[$sec->id] = $sec->students->pluck('id')->toArray();
                                }
                            ?>

                            
                            <?php if($sections->isNotEmpty()): ?>
                            <div style="margin-bottom:.65rem;">
                                <label style="font-size:.78rem;font-weight:600;color:#374151;display:block;margin-bottom:.3rem;">
                                    🏷️ Filter by Section
                                </label>
                                <div style="position:relative;">
                                    <select id="studentSectionFilter"
                                        onchange="filterStudentsBySection(this.value)"
                                        style="width:100%;padding:.5rem 2rem .5rem .8rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;background:#fff;appearance:none;cursor:pointer;">
                                        <option value="">— All sections (show everyone) —</option>
                                        <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($sec->id); ?>"><?php echo e($sec->name); ?><?php echo e($sec->school_year ? " · " . $sec->school_year : ""); ?><?php echo e($sec->semester ? " · " . $sec->semester : ""); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <span style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none;">▾</span>
                                </div>
                            </div>
                            <?php endif; ?>

                            
                            <input type="text" id="studentSearchProj"
                                placeholder="🔍 Search by name or ID..."
                                oninput="filterStudentsProj()"
                                style="width:100%;padding:.5rem .85rem;border:1.5px solid #e5e7eb;border-radius:8px;font-size:.85rem;box-sizing:border-box;margin-bottom:.5rem;">

                            <div style="padding:.75rem 1rem;background:#f9fafb;border:1.5px solid #e5e7eb;border-radius:10px;max-height:260px;overflow-y:auto;" id="studentCheckList">
                                <div style="font-size:.78rem;color:#6b7280;margin-bottom:.5rem;">Select students to assign to this project:</div>
                                <?php $__currentLoopData = $myStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $studentSecs = [];
                                        foreach($secStudentMap as $secId => $stuIds) {
                                            if(in_array($st->id, $stuIds)) $studentSecs[] = $secId;
                                        }
                                    ?>
                                    <label class="proj-student-row"
                                           data-name="<?php echo e(strtolower($st->name)); ?>"
                                           data-sid="<?php echo e(strtolower($st->student_id ?? '')); ?>"
                                           data-uid="<?php echo e($st->id); ?>"
                                           data-sections="<?php echo e(implode(',', $studentSecs)); ?>"
                                           style="display:flex;align-items:center;gap:.6rem;padding:.45rem .2rem;cursor:pointer;border-bottom:1px solid #f3f4f6;">
                                        <input type="checkbox"
                                               name="student_ids[]"
                                               value="<?php echo e($st->id); ?>"
                                               <?php echo e(in_array($st->id, old('student_ids', [])) ? 'checked' : ''); ?>

                                               style="width:16px;height:16px;accent-color:#4f46e5;flex-shrink:0;">
                                        <div style="flex:1;min-width:0;">
                                            <div style="font-weight:600;font-size:.87rem;display:flex;align-items:center;gap:.35rem;flex-wrap:wrap;">
                                                <?php echo e($st->name); ?>

                                                <?php if(!empty($studentSecs)): ?>
                                                    <?php $__currentLoopData = $sections->whereIn('id', $studentSecs); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ss): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span style="padding:.1rem .4rem;background:#eef2ff;color:#4f46e5;border-radius:999px;font-size:.65rem;font-weight:700;"><?php echo e($ss->name); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if($st->student_id): ?>
                                                <div style="font-size:.72rem;color:#9ca3af;">🆔 <?php echo e($st->student_id); ?> · <?php echo e($st->department ?? 'No course'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <div id="projNoMatch" style="display:none;padding:1rem;text-align:center;color:#9ca3af;font-size:.82rem;">No students match.</div>
                            </div>
                            <div style="margin-top:.5rem;font-size:.75rem;color:#6b7280;">
                                Each selected student will receive this project individually and be graded separately.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <div class="field">

                    <label>Start Date</label>

                    <input
                        type="datetime-local"
                        name="start_date"
                        value="<?php echo e(old('start_date')); ?>"
                        required
                    >
                </div>

                
                <div class="field">

                    <label>Due Date</label>

                    <input
                        type="datetime-local"
                        name="due_date"
                        value="<?php echo e(old('due_date')); ?>"
                        required
                    >
                </div>

                
                <div class="field">

                    <label>Status</label>

                    <select
                        name="status"
                        required
                    >
                        <option
                            value="draft"
                            <?php echo e(old('status', 'draft') == 'draft' ? 'selected' : ''); ?>

                        >
                            Draft
                        </option>

                        <option
                            value="published"
                            <?php echo e(old('status') == 'published' ? 'selected' : ''); ?>

                        >
                            Published
                        </option>

                        <option
                            value="ongoing"
                            <?php echo e(old('status') == 'ongoing' ? 'selected' : ''); ?>

                        >
                            Ongoing
                        </option>

                        <option
                            value="completed"
                            <?php echo e(old('status') == 'completed' ? 'selected' : ''); ?>

                        >
                            Completed
                        </option>

                    </select>
                </div>

                
                <hr style="margin:2rem 0;border:none;border-top:1px solid #e5e7eb;">

                <div class="field">

                    <label style="font-size:1rem;font-weight:700;">
                        📋 Assign Tasks
                    </label>

                    <div id="task-wrapper">

                        
                        <div class="task-card">

                            <div class="field">
                                <label>Task Title</label>

                                <input
                                    type="text"
                                    name="tasks[0][title]"
                                    placeholder="Enter task title"
                                >
                            </div>

                            <div class="field">
                                <label>Task Description</label>

                                <textarea
                                    name="tasks[0][description]"
                                    rows="3"
                                    placeholder="Enter task details"
                                ></textarea>
                            </div>

                            <div class="field">
                                <label>Task Due Date</label>
                                <input type="datetime-local" name="tasks[0][due_date]">
                            </div>

                            <div class="field">
                                <label>Max Points
                                    <span style="color:#9ca3af;font-weight:400;">(default: 100)</span>
                                </label>
                                <input type="number"
                                       name="tasks[0][max_points]"
                                       min="1"
                                       max="10000"
                                       placeholder="100"
                                       value="<?php echo e(old('tasks.0.max_points', 100)); ?>"
                                       style="width:100%;">
                            </div>

                        </div>

                    </div>

                    
                    <button
                        type="button"
                        class="btn btn-add"
                        id="add-task-btn"
                    >
                        ➕ Add Another Task
                    </button>

                </div>

                
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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('assets/js/pages/teacher-project-create.js')); ?>"></script>
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
<?php if(old('student_ids')): ?>
    switchAssignMode('individual');
<?php endif; ?>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\amo-guro-ni\resources\views/teacher/projects/create.blade.php ENDPATH**/ ?>