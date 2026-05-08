// Mensajen Advanced JS - Phase 4
document.addEventListener('DOMContentLoaded', function() {
    // Toast notifications (SweetAlert)
    function showToast(icon, title, message, timer = 4000) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            text: message,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            },
            customClass: {
                popup: 'animate__animated animate__fadeInRight'
            }
        });
    }

    // Reply templates
    const replyTemplates = {
        obrigado: 'Obrigadu tebes ba kontaktu ami. Ami hetan mensajen ida ne’e no sei responde lalais liu ida posible.',
        emAndamentu: 'Obrigadu ba informasaun importante ne’e. Kazu ne’e iha em andamentu no ami sei hetan kontaktu fali ona.',
        resolvido: 'Obrigadu! Kazu ne’e resolvido ona. Se iha pergunta seluk, favor haruka mensajen foun.',
        agradecimento: 'Agradesimentu tebes ba kontribuisaun importante ida ne’e ba seguransa komunitáriu.',
        infoAdicional: 'Pode fornese informasaun adicional kona-ba problema ne’e? Ami presiza detalhe liu hodi ajuda efisiente.'
    };

    // Add template buttons to all reply forms
    document.querySelectorAll('.modal textarea[name=\"reply_body\"]').forEach(textarea => {
        const container = textarea.closest('.mb-4');
        const templateBtnGroup = document.createElement('div');
        templateBtnGroup.className = 'd-flex flex-wrap gap-1 mb-3';
        templateBtnGroup.innerHTML = `
            <button type="button" class="reply-template-btn" onclick="insertTemplate(this, 'obrigado')">Obrigadu</button>
            <button type="button" class="reply-template-btn" onclick="insertTemplate(this, 'emAndamentu')">Em Andamentu</button>
            <button type="button" class="reply-template-btn" onclick="insertTemplate(this, 'resolvido')">Resolvido</button>
            <button type="button" class="reply-template-btn" onclick="insertTemplate(this, 'agradecimento')">Agradesimentu</button>
        `;
        container.insertBefore(templateBtnGroup, textarea);
    });

    // Insert template
    window.insertTemplate = function(btn, key) {
        const textarea = btn.closest('.mb-4').querySelector('textarea[name=\"reply_body\"]');
        textarea.value = replyTemplates[key];
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        showToast('info', 'Template inseridu', 'Pode edita mensagem antes haruka!');
    };

    // Form success/error handling (no reload)
    document.querySelectorAll('form[action*=\"mensajen/view\"]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type=\"submit\"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class=\"fa fa-spinner fa-spin me-2\"></i> Haruka...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    showToast('success', 'Susesu!', 'Resposta haruka ona ba email!', 5000);
                    refreshUnreadCount(); // Update badge
                    bootstrap.Modal.getInstance(this.closest('.modal')).hide();
                    setTimeout(() => location.reload(), 1000); // Soft reload
                }
            } catch (error) {
                showToast('error', 'Erro!', 'Faila haruka resposta. Tenta fali.');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    });

    // Bulk select (checkboxes + actions)
    const thead = document.querySelector('#mensajenTable thead tr');
    thead.insertCell(0).outerHTML = '<th><input type="checkbox" id="selectAll"></th>';

    rows.forEach((row, index) => {
        const checkboxCell = document.createElement('td');
        checkboxCell.innerHTML = '<input type="checkbox" class="bulk-select" value="' + index + '">';
        row.insertBefore(checkboxCell, row.firstChild);
    });

    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('.bulk-select').forEach(cb => cb.checked = this.checked);
    });

    // Bulk actions (add button later)

    // Success/Error toasts from PHP sessions
    <?php if ($reply_success): ?>
    showToast('success', 'Susesu!', '<?= __('
        Susesu_Manda ') ?>');
    <?php endif; ?>
    <?php if ($reply_error): ?>
    showToast('error', 'Erro!', '<?= $reply_error ?>');
    <?php endif; ?>

})(); <
/script>