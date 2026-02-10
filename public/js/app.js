document.addEventListener('DOMContentLoaded', function () {

    var menuToggle = document.getElementById('menuToggle');
    var sidebar = document.getElementById('sidebar');
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
        });
    }

    function setupDropdown(btnId, ddId) {
        var btn = document.getElementById(btnId);
        var dd = document.getElementById(ddId);
        if (!btn || !dd) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.notif-dropdown.show, .profile-dropdown.show').forEach(function (el) {
                if (el !== dd) el.classList.remove('show');
            });
            dd.classList.toggle('show');
        });
    }
    setupDropdown('btnNotif', 'notifDropdown');
    setupDropdown('btnProfile', 'profileDropdown');

    document.addEventListener('click', function (e) {
        if (sidebar && menuToggle && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
        if (!e.target.closest('.notif-wrapper') && !e.target.closest('.profile-wrapper')) {
            document.querySelectorAll('.notif-dropdown.show, .profile-dropdown.show').forEach(function (el) {
                el.classList.remove('show');
            });
        }
    });

    function setupModal(openBtnId, modalId, closeId, cancelId) {
        var btn = document.getElementById(openBtnId);
        var modal = document.getElementById(modalId);
        var closeBtn = document.getElementById(closeId);
        var cancelBtn = document.getElementById(cancelId);
        if (!btn || !modal) return;

        btn.addEventListener('click', function () {
            modal.classList.add('show');
        });
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                modal.classList.remove('show');
            });
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                modal.classList.remove('show');
            });
        }
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('show');
            }
        });
    }
    setupModal('btnAddObjet', 'modalObjet', 'modalClose', 'modalCancel');
    setupModal('btnProposeEchange', 'modalEchange', 'modalEchangeClose', 'modalEchangeCancel');

    /* ===== UPLOAD PHOTOS MULTIPLES ===== */
    var photoInput = document.getElementById('photoInput');
    var btnPhotos = document.getElementById('btnSelectPhotos');
    var photoPreview = document.getElementById('photoPreview');
    var selectedFiles = [];

    if (btnPhotos && photoInput) {
        btnPhotos.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            photoInput.click();
        });

        photoInput.addEventListener('change', function () {
            var newFiles = Array.from(this.files);
            for (var i = 0; i < newFiles.length; i++) {
                selectedFiles.push(newFiles[i]);
            }
            renderPreviews();
            this.value = '';
        });
    }

    function renderPreviews() {
        if (!photoPreview) return;
        photoPreview.innerHTML = '';
        for (var i = 0; i < selectedFiles.length; i++) {
            (function (index) {
                var reader = new FileReader();
                reader.onload = function (ev) {
                    var thumb = document.createElement('div');
                    thumb.className = 'photo-thumb';
                    thumb.innerHTML =
                        '<img src="' + ev.target.result + '" alt="preview">' +
                        '<button type="button" class="photo-remove" data-index="' + index + '">&times;</button>';
                    photoPreview.appendChild(thumb);
                };
                reader.readAsDataURL(selectedFiles[index]);
            })(i);
        }
    }

    if (photoPreview) {
        photoPreview.addEventListener('click', function (e) {
            var target = e.target;
            if (target.classList.contains('photo-remove')) {
                var idx = parseInt(target.getAttribute('data-index'), 10);
                selectedFiles.splice(idx, 1);
                renderPreviews();
            }
        });
    }


    var formAdd = document.getElementById('formAddObjet');
    if (formAdd) {
        formAdd.addEventListener('submit', function (e) {
            e.preventDefault();

            var titre = formAdd.querySelector('[name="titre"]').value.trim();
            var prix = formAdd.querySelector('[name="prix"]').value;
            var cat = formAdd.querySelector('[name="idCategorie"]').value;

            if (!titre || !prix || !cat) {
                alert('Veuillez remplir tous les champs obligatoires (titre, prix, categorie).');
                return;
            }
            if (parseFloat(prix) <= 0) {
                alert('Le prix doit etre superieur a 0.');
                return;
            }

            var formData = new FormData(formAdd);


            formData.delete('photos[]');
            console.log('Selected files:', selectedFiles);
            for (var i = 0; i < selectedFiles.length; i++) {
                formData.append('photos[]', selectedFiles[i], selectedFiles[i].name);
                console.log('Appending file:', selectedFiles[i].name);
            }

            var submitBtn = formAdd.querySelector('[type="submit"]');
            var originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Publication en cours...';

            fetch('/objets/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.href = data.redirect || '/profil?success=objet_ajoute';
                } else {
                    alert(data.message || "Erreur lors de l'ajout.");
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                }
            })
            .catch(function (err) {
                console.error('Fetch Error:', err);
                alert("Une erreur technique est survenue.");
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }


    var galleryThumbs = document.querySelectorAll('.gallery-thumb');
    var mainPhoto = document.getElementById('mainPhoto');
    if (galleryThumbs.length > 0 && mainPhoto) {
        galleryThumbs.forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                mainPhoto.src = this.getAttribute('data-src');
                galleryThumbs.forEach(function (t) { t.classList.remove('active'); });
                this.classList.add('active');
            });
        });
    }
});
