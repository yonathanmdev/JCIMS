document.addEventListener('DOMContentLoaded', function () {

  // ─── DOM ELEMENTS (ከ var ይልቅ በ const ተተክተዋል - መረጃቸው አይቀየርም) ───
  const certTypeSelect  = document.getElementById('certificate_type');
  const otherGroup      = document.getElementById('other_type_group');
  const otherInput      = document.getElementById('certificate_type_other');
  const fileInput       = document.getElementById('attachment');
  const modal           = document.getElementById('fileAttachmentModal');
  const modalTitle      = document.getElementById('modalTitle');
  const formMode        = document.getElementById('formMode');
  const documentId      = document.getElementById('documentId');
  const submitBtn       = document.getElementById('submitBtn');
  const editFileNote    = document.getElementById('editFileNote');
  const fileRequired    = document.getElementById('fileRequired');
  const documentForm    = document.getElementById('documentForm');

  // ─── Certificate type "ሌላ" toggle ───────────────────────────
  if (certTypeSelect) {
    certTypeSelect.addEventListener('change', function () {
      if (this.value === 'ሌላ') {
        otherGroup.style.display = 'block';
        otherInput.disabled      = false;
        otherInput.required      = true;
      } else {
        otherGroup.style.display = 'none';
        otherInput.disabled      = true;
        otherInput.required      = false;
        otherInput.value         = '';
      }
    });
  }

  // ─── Custom file label update ────────────────────────────────
  if (fileInput) {
    fileInput.addEventListener('change', function () {
      const label = this.nextElementSibling;
      if (label) {
        label.textContent = this.files[0]
          ? this.files[0].name
          : 'ፋይል ይምረጡ (PDF/Image)...';
      }
    });
  }

  // ─── Reset modal on close ────────────────────────────────────
  if (modal) {
    // AdminLTE 3 / Bootstrap 4 ለሞዳል መዝጊያ 'hidden.bs.modal' ይጠቀማል
    modal.addEventListener('hidden.bs.modal', function () {
      resetModal();
    });
  }

  // ─── 1. "ፋይል መዝግብ" (UPLOAD) ቁልፍ ሲጫን (Event Listener) ──────
  const btnUpload = document.getElementById('btnOpenUploadModal');
  if (btnUpload) {
    btnUpload.addEventListener('click', function () {
      if (fileInput) fileInput.required = true;
    });
  }

  // ─── 2. "አስተካክል" (EDIT) ቁልፎች ሲጫኑ (Event Listener) ─────────
  // በሰንጠረዡ (Table) ውስጥ ያሉትን ሁሉንም የአርትዕ ቁልፎች በአንድ ላይ ይከታተላል
  document.querySelectorAll('.btn-edit-document').forEach(function (button) {
    button.addEventListener('click', function () {
      // በ HTML data-* attributes የተጫኑትን መረጃዎች በደህንነት መንገድ ያነባል
      const docId = this.getAttribute('data-id');
      const entityType = this.getAttribute('data-type');
      
      // የ Edit ሁኔታን የሚያስነሳውን የውስጥ ተግባር መጥራት
      openEditModal(docId, entityType);
    });
  });

  // ─── Open in EDIT mode (Internal Helper Function) ────────────
  function openEditModal(docId, entityType) {
    if (!formMode || !documentId || !modalTitle || !submitBtn || !fileInput) return;

    // የፎርሙን ሁኔታ ወደ edit ይቀይራል
    formMode.value   = 'edit';
    documentId.value = docId;

    // የሞዳሉን ርዕስ እና ቁልፍ ስም ይቀይራል
    modalTitle.innerHTML = '<i class="fas fa-edit mr-1"></i> ፋይል አያይዝ';
    submitBtn.innerHTML  = '<i class="fas fa-save mr-1"></i> አስተካክል';

    // የነበረውን የምስክር ወረቀት አይነት ዝርዝር ውስጥ ፈልጎ መምረጥ (Select)
    if (certTypeSelect) {
      const options = certTypeSelect.options;
      let matched = false; // መረጃው የሚቀየር በመሆኑ በ let ተፈጥሯል
      
      for (let i = 0; i < options.length; i++) {
        if (options[i].value === entityType) {
          certTypeSelect.value = entityType;
          matched = true;
          break;
        }
      }

      // መረጃው በዝርዝሩ ውስጥ ከሌለ "ሌላ" የሚለውን መርጦ ጽሁፉን ያስገባል
      if (!matched && entityType) {
        certTypeSelect.value = 'ሌላ';
        if (otherGroup) otherGroup.style.display = 'block';
        if (otherInput) {
          otherInput.disabled         = false;
          otherInput.required         = true;
          otherInput.value            = entityType;
        }
      }
    }

    // በ Edit ጊዜ አዲስ ፋይል መጫን ግዴታ አይደለም (የድሮው ፋይል ሊቆይ ስለሚችል)
    fileInput.required = false;
    if (editFileNote) editFileNote.style.display = 'block';
    if (fileRequired) fileRequired.style.display = 'none';
  }

  // ─── Helper: reset everything ────────────────────────────────
  function resetModal() {
    if (documentForm)   documentForm.reset(); // ሙሉ ፎርሙን ባዶ ያደርጋል
    if (certTypeSelect) certTypeSelect.value = '';
    if (otherGroup)     otherGroup.style.display = 'none';
    if (otherInput) {
      otherInput.disabled = true;
      otherInput.required = false;
      otherInput.value    = '';
    }
    if (fileInput) {
      fileInput.value    = '';
      fileInput.required = true;
      const label = fileInput.nextElementSibling;
      if (label) label.textContent = 'ፋይል ይምረጡ (PDF/Image)...';
    }
    if (editFileNote)  editFileNote.style.display  = 'none';
    if (fileRequired)  fileRequired.style.display  = 'inline';
    if (formMode)      formMode.value              = 'upload';
    if (documentId)    documentId.value            = '';
    if (modalTitle)    modalTitle.innerHTML        = '<i class="fas fa-plus mr-1"></i> መረጃ ማህደር ጋር ማያያዝ';
    if (submitBtn)     submitBtn.innerHTML         = '<i class="fas fa-save mr-1"></i> መዝግብ';
  }

});