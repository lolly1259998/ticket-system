<?php $css_file = 'tickets.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>

<div class="content">
    <div class="page-header">
        <h1>Ticket #<?php echo $ticket['id']; ?> - <?php echo htmlspecialchars($ticket['sujet']); ?></h1>
        <div>
            <a href="index.php?action=edit_ticket&id=<?php echo $ticket['id']; ?>" class="btn-primary">Modifier</a>
            <a href="index.php?action=ticket_list" class="btn-primary">Retour à la liste</a>
        </div>
    </div>

    <!-- Onglets de navigation -->
    <div class="tabs">
        <a class="tab <?php echo $active_section==='resume' ? 'active' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=resume">Résumé</a>
        <a class="tab <?php echo $active_section==='statut' ? 'active' : ''; ?> <?php echo (!$can_update || empty($next_statuses)) ? 'disabled' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=statut">Changer Statut</a>
        <a class="tab <?php echo $active_section==='commentaires' ? 'active' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=commentaires">Commentaires</a>
        <a class="tab <?php echo $active_section==='solutions' ? 'active' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=solutions">Solutions proposées</a>
        <a class="tab <?php echo $active_section==='historique' ? 'active' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=historique">Historique du ticket</a>
        <a class="tab <?php echo $active_section==='avis' ? 'active' : ''; ?>" href="index.php?action=ticket_detail&id=<?php echo $ticket['id']; ?>&section=avis">Historique de l'avis</a>
    </div>

    <!-- Informations du ticket -->
    <?php if($active_section === 'resume'): ?>
    <div class="ticket-detail">
        <div class="ticket-info">
            <div class="info-group">
                <label>Tracker</label>
                <span><?php echo $ticket['tracker']; ?></span>
            </div>
            <div class="info-group">
                <label>Statut</label>
                <?php $status_slug_map = ['Nouveau'=>'nouveau','En cours'=>'en-cours','Résolu'=>'resolu','Fermé'=>'ferme']; $slug = $status_slug_map[$ticket['status']] ?? strtolower(str_replace(' ', '-', $ticket['status'])); ?>
                <span class="ticket-status status-<?php echo $slug; ?>">
                    <?php echo $ticket['status']; ?>
                </span>
            </div>
            <div class="info-group">
                <label>Priorité</label>
                <span><?php echo $ticket['priority']; ?></span>
            </div>
            <div class="info-group">
                <label>Assigné à</label>
                <span><?php echo $ticket['assigned_username'] ?? 'Non assigné'; ?></span>
            </div>
            <div class="info-group">
                <label>Créé par</label>
                <span><?php echo $ticket['created_username']; ?></span>
            </div>
            <div class="info-group">
                <label>Date de début</label>
                <span><?php echo date('d/m/Y', strtotime($ticket['date_debut'])); ?></span>
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <div class="ticket-description">
                <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
            </div>
        </div>

        <?php if($ticket['fichier']): ?>
            <div class="form-group">
                <label>Fichier joint</label>
                <a href="uploads/<?php echo $ticket['fichier']; ?>" target="_blank" style="color: var(--red);">
                    Télécharger le fichier
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Workflow - Changement de statut -->
    <?php if($active_section === 'statut'): ?>
    <?php if($can_update && !empty($next_statuses)): ?>
    <div class="history-section">
        <h3 class="section-title">Changer le statut</h3>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Nouveau statut</label>
                    <select name="status" id="status" required>
                        <option value="">Sélectionnez un statut</option>
                        <?php foreach($next_statuses as $status): ?>
                            <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status_comment">Commentaire (optionnel)</label>
                    <input type="text" name="status_comment" id="status_comment" placeholder="Raison du changement">
                </div>
            </div>
            <button type="submit" name="update_status" class="btn-primary">Mettre à jour le statut</button>
        </form>
    </div>
    <?php else: ?>
        <p style="color:#6c757d;">Vous ne pouvez pas modifier le statut de ce ticket.</p>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Commentaires -->
    <?php if($active_section === 'commentaires'): ?>
    <div class="comments-section">
        <h3 class="section-title">Commentaires</h3>
        
        <!-- Formulaire d'ajout de commentaire -->
        <form method="POST" action="" style="margin-bottom: 2rem;">
            <div class="form-group">
                <label for="comment">Ajouter un commentaire</label>
                <textarea name="comment" id="comment" required placeholder="Votre commentaire..."></textarea>
            </div>
            <button type="submit" name="add_comment" class="btn-primary">Ajouter le commentaire</button>
        </form>

        <!-- Liste des commentaires -->
        <?php if(!empty($comments)): ?>
            <?php foreach($comments as $comment): ?>
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                        <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                    </div>
                    <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun commentaire pour le moment.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Solutions proposées -->
    <?php if($active_section === 'solutions'): ?>
    <div class="solutions-section">
        <h3 class="section-title">Solutions proposées</h3>
        
        <!-- Formulaire d'ajout de solution -->
        <form method="POST" action="" style="margin-bottom: 2rem;">
            <div class="form-group">
                <label for="solution">Proposer une solution</label>
                <textarea name="solution" id="solution" required placeholder="Décrivez votre solution..."></textarea>
            </div>
            <button type="submit" name="add_solution" class="btn-primary">Proposer la solution</button>
        </form>

        <!-- Liste des solutions -->
        <?php if(!empty($solutions)): ?>
            <?php foreach($solutions as $solution): ?>
                <div class="solution-item">
                    <div class="solution-header">
                        <span class="solution-author"><?php echo htmlspecialchars($solution['username']); ?></span>
                        <span class="solution-date"><?php echo date('d/m/Y H:i', strtotime($solution['created_at'])); ?></span>
                    </div>
                    <div class="solution-text"><?php echo nl2br(htmlspecialchars($solution['solution'])); ?></div>
                    
                    <?php 
                        // Tri-état: NULL = non décidé (afficher les deux boutons),
                        // 1 = acceptée (afficher résultat uniquement),
                        // 0 = refusée (afficher résultat uniquement)
                        $state = array_key_exists('is_accepted', $solution) ? $solution['is_accepted'] : null; 
                    ?>

                    <?php if($state === 1 || $state === '1'): ?>
                        <div class="solution-label-accepted">✓ Solution acceptée</div>
                    <?php elseif($state === 0 || $state === '0'): ?>
                        <div class="solution-label-rejected">✗ Solution non acceptée</div>
                    <?php else: ?>
                        <div class="solution-label-rejected">✗ Solution non acceptée</div>
                        <?php if($can_update): ?>
                        <div class="solution-actions">
                            <button type="button" class="btn-accept open-feedback" data-sid="<?php echo $solution['id']; ?>">Accepter cette solution</button>
                            <form method="POST" action="">
                                <input type="hidden" name="solution_id" value="<?php echo $solution['id']; ?>">
                                <button type="submit" name="reject_solution" class="btn-reject">Ne pas accepter</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune solution proposée pour le moment.</p>
        <?php endif; ?>
        <!-- Modal Feedback -->
        <div id="feedbackModal" class="modal-overlay">
            <div class="modal">
                <h3>Votre avis</h3>
                <form method="POST" action="">
                    <input type="hidden" name="accept_solution" value="1">
                    <input type="hidden" name="solution_id" id="feedback_solution_id" value="">
                    <div class="form-group">
                        <label>Degré de satisfaction (1 à 10)</label>
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <input type="range" name="degre_satisfaction" id="fs_satisfaction" min="1" max="10" value="8" class="range-input" style="flex:1;">
                            <span id="fs_satisfaction_val">8</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>La solution a-t-elle résolu votre problème ?</label>
                        <div style="display:flex; gap:1rem;">
                            <label><input type="radio" name="resolved" value="oui" checked> Oui</label>
                            <label><input type="radio" name="resolved" value="non"> Non</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Recommanderiez-vous notre service ? (1 à 10)</label>
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <input type="range" name="recommend" id="fs_recommend" min="1" max="10" value="8" class="range-input" style="flex:1;">
                            <span id="fs_recommend_val">8</span>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" id="feedbackCancel">Annuler</button>
                        <button type="submit" class="btn-primary">Valider</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        (function(){
          var modal = document.getElementById('feedbackModal');
          var cancel = document.getElementById('feedbackCancel');
          var sidInput = document.getElementById('feedback_solution_id');
          var sat = document.getElementById('fs_satisfaction');
          var satVal = document.getElementById('fs_satisfaction_val');
          var rec = document.getElementById('fs_recommend');
          var recVal = document.getElementById('fs_recommend_val');
          function openModal(id){ sidInput.value = id; modal.style.display='flex'; }
          function closeModal(){ modal.style.display='none'; }
          document.querySelectorAll('.open-feedback').forEach(function(btn){
            btn.addEventListener('click', function(){ openModal(this.getAttribute('data-sid')); });
          });
          cancel.addEventListener('click', function(){ closeModal(); });
          [sat, rec].forEach(function(input){
            if(input){
              input.addEventListener('input', function(){
                if(this.id==='fs_satisfaction'){ satVal.textContent = this.value; }
                if(this.id==='fs_recommend'){ recVal.textContent = this.value; }
              });
            }
          });
          window.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closeModal(); } });
        })();
        </script>
    </div>
    <?php endif; ?>

    <!-- Activité par utilisateur -->
    <?php if($active_section === 'resume'): ?>
    <div class="activity-section">
        <h3 class="section-title">Activité par utilisateur</h3>
        <?php if(!empty($activity_by_user)): ?>
            <div class="activity-grid">
                <?php foreach($activity_by_user as $username => $stats): ?>
                    <div class="activity-item">
                        <div class="activity-header">
                            <span class="activity-user"><?php echo htmlspecialchars($username); ?></span>
                            <?php if(!empty($stats['last_date'])): ?>
                                <span class="activity-date">Dernière activité: <?php echo date('d/m/Y H:i', strtotime($stats['last_date'])); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="activity-counts">
                            <span class="activity-badge">Historique: <?php echo (int)$stats['history']; ?></span>
                            <span class="activity-badge">Commentaires: <?php echo (int)$stats['comments']; ?></span>
                            <span class="activity-badge">Solutions: <?php echo (int)$stats['solutions']; ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucune activité pour le moment.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Historique du ticket -->
    <?php if($active_section === 'historique'): ?>
    <div class="history-section">
        <h3 class="section-title">Historique du Ticket</h3>
        <?php if(!empty($history)): ?>
            <?php foreach($history as $item): ?>
                <div class="history-item">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($item['username']); ?></span>
                        <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></span>
                    </div>
                    <div><strong><?php echo $item['action']; ?>:</strong> <?php echo htmlspecialchars($item['description']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun historique disponible.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Historique de l'avis -->
    <?php if($active_section === 'avis'): ?>
    <div class="history-section">
        <h3 class="section-title">Historique de l'avis</h3>
        <?php 
            $avis = array_filter($history, function($h){ return isset($h['action']) && $h['action'] === 'Solution acceptée'; });
        ?>
        <?php if(!empty($avis)): ?>
            <?php foreach($avis as $item): ?>
                <div class="history-item">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($item['username']); ?></span>
                        <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></span>
                    </div>
                    <div><strong><?php echo $item['action']; ?>:</strong> <?php echo htmlspecialchars($item['description']); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun avis disponible.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'views/layout/footer.php'; ?>
