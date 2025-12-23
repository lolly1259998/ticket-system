<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content page-ticket-create">
    <div class="page-header">
        <h1>Créer un Nouveau Ticket</h1>
        <a href="index.php?action=ticket_list" class="btn-primary">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="card">
        <form method="POST" action="" enctype="multipart/form-data" class="ticket-form">
            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 14.5a6.5 6.5 0 1 1 0-13 6.5 6.5 0 0 1 0 13zm-1-9.5a1 1 0 1 1 2 0v4a1 1 0 1 1-2 0V7zm1 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    Informations générales
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tracker" class="form-label">Tracker <span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select name="tracker" id="tracker" required>
                                <option value="">Sélectionnez un tracker</option>
                                <option value="Bug">Bug</option>
                                <option value="Feature">Feature</option>
                                <option value="Support">Support</option>
                                <option value="Webmastering">Webmastering</option>
                                <option value="Security">Security</option>
                                <option value="Audit">Audit</option>
                            </select>
                            <div class="select-arrow"></div>
                        </div>
                        <small class="form-help">Choisissez le type de ticket</small>
                    </div>

                    <div class="form-group">
                        <label for="priority" class="form-label">Priorité <span class="required">*</span></label>
                        <div class="select-wrapper">
                            <select name="priority" id="priority" required>
                                <option value="Basse">Basse</option>
                                <option value="Normale" selected>Normale</option>
                                <option value="Haute">Haute</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                            <div class="select-arrow"></div>
                        </div>
                        <small class="form-help">Définissez l'urgence</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17 4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14zm-1 2H4v8h12V6zm-8 2h6v1H8V8zm0 2h6v1H8v-1zm0 2h4v1H8v-1z"/>
                    </svg>
                    Contenu du ticket
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="sujet" class="form-label">Sujet <span class="required">*</span></label>
                        <input type="text" name="sujet" id="sujet" required placeholder="Ex: Problème de connexion au portail">
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">Description <span class="required">*</span></label>
                        <textarea name="description" id="description" required placeholder="Expliquez clairement le problème ou la demande, étapes pour reproduire, impact, etc." rows="6"></textarea>
                        <small class="form-help">Ajoutez le maximum de détails utiles</small>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm0 1a7 7 0 1 1 0 14 7 7 0 0 1 0-14zm0 10.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm0-8a1 1 0 0 0-1 1v4a1 1 0 1 0 2 0V5.5a1 1 0 0 0-1-1z"/>
                    </svg>
                    Options supplémentaires
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="assigned_to" class="form-label">Assigné à</label>
                        <div class="select-wrapper">
                            <select name="assigned_to" id="assigned_to">
                                <option value="">Non assigné</option>
                                <?php foreach($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="select-arrow"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="date_debut" class="form-label">Date de début <span class="required">*</span></label>
                        <div class="input-with-icon">
                            <input type="date" name="date_debut" id="date_debut" required value="<?php echo date('Y-m-d'); ?>">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M14 2h-1V1a1 1 0 0 0-2 0v1H5V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM2 3h12a1 1 0 0 1 1 1v1H1V4a1 1 0 0 1 1-1zm12 12H2a1 1 0 0 1-1-1V7h14v7a1 1 0 0 1-1 1z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="fichier" class="form-label">Fichier joint</label>
                        <div class="file-upload">
                            <input type="file" name="fichier" id="fichier" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg">
                            <label for="fichier" class="file-label">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9 2a1 1 0 0 0-.8.4L6.4 4.8A1 1 0 0 0 6 5H3a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1h-3a1 1 0 0 0-.8-.4L11.4 2.4A1 1 0 0 0 11 2H9zm0 1h2l1.6 2.4a1 1 0 0 0 .8.4h3v9H3V6h3a1 1 0 0 0 .8-.4L8.4 3.4A1 1 0 0 0 9 3z"/>
                                    <path d="M10 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm0 1a2 2 0 1 1 0 4 2 2 0 0 1 0-4z"/>
                                </svg>
                                <span>Choisir un fichier</span>
                            </label>
                            <div class="file-name" id="fileName">Aucun fichier sélectionné</div>
                        </div>
                        <small class="form-help">Formats: PDF, DOC(X), PNG, JPG. Taille raisonnable.</small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
               
                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M14 2h-1V1a1 1 0 0 0-2 0v1H5V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM2 3h12a1 1 0 0 1 1 1v1H1V4a1 1 0 0 1 1-1zm12 12H2a1 1 0 0 1-1-1V7h14v7a1 1 0 0 1-1 1z"/>
                        <path d="M4 10h8v1H4v-1z"/>
                    </svg>
                    Créer le Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Affichage du nom du fichier sélectionné
    const fileInput = document.getElementById('fichier');
    const fileName = document.getElementById('fileName');
    
    if (fileInput && fileName) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'Aucun fichier sélectionné';
            }
        });
    }
</script>

<?php include 'views/layout/footer.php'; ?>
