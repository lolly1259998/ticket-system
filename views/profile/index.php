<?php $css_file = 'profile.css'; ?>
<?php include 'views/layout/header.php'; ?>
<?php include 'views/layout/sidebar.php'; ?>
<div class="content">
    <?php if(isset($success) && $success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if(isset($error) && $error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <script>
    (function(){
      var els = document.querySelectorAll('.alert-success');
      els.forEach(function(el){
        setTimeout(function(){
          el.style.transition = 'opacity .4s ease';
          el.style.opacity = '0';
          setTimeout(function(){ if(el && el.parentNode){ el.parentNode.removeChild(el); } }, 400);
        }, 3000);
      });
    })();
    </script>

    <div class="profile-wrapper">
        <div class="profile-card">
            <div class="profile-top">
                <div class="profile-avatar">
                    <?php if(!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?php echo $basePath; ?>uploads/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" alt="avatar">
                    <?php else: ?>
                        <i class="fas fa-user" style="font-size:42px;color:#fff;"></i>
                    <?php endif; ?>
                    <label class="avatar-edit" for="avatar_input"><i class="fas fa-pen"></i></label>
                </div>
                <div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_avatar">
                <input id="avatar_input" type="file" name="avatar" accept="image/*" style="display:none;">
               
            </form>
            <script>
            (function(){
              var input = document.getElementById('avatar_input');
              if(input){
                input.addEventListener('change', function(){
                  if(this.files && this.files.length > 0){
                    var f = this.closest('form');
                    if(f){ f.submit(); }
                  }
                });
              }
            })();
            </script>

            <div class="profile-fields">
                <div class="input-row">
                    <div class="input-group">
                        <label>Utilisateur :</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                    </div>
                    <div class="input-group">
                        <label>Email :</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                </div>
                <div class="input-row">
                    <div class="input-group">
                        <label>Password :</label>
                        <input type="password" value="" placeholder="********">
                    </div>
                </div>
            </div>

            <div class="password-actions">
                <button type="button" id="toggle_pwd" class="btn-primary">Modifier le mot de passe</button>
                <form id="pwd_form" method="POST" action="" style="display:none; margin-top:0.75rem;">
                    <input type="hidden" name="action" value="change_password">
                    <div class="input-row">
                        <div class="input-group">
                            <label>Actuel :</label>
                            <input id="pwd_current" type="password" name="current_password" required>
                            <button type="button" class="eye-toggle" data-target="pwd_current"><i class="fas fa-eye"></i></button>
                        </div>
                        <div class="input-group">
                            <label>Nouveau :</label>
                            <input id="pwd_new" type="password" name="new_password" minlength="6" required>
                            <button type="button" class="eye-toggle" data-target="pwd_new"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="input-row">
                        <div class="input-group">
                            <label>Confirmer :</label>
                            <input id="pwd_confirm" type="password" name="confirm_password" minlength="6" required>
                            <button type="button" class="eye-toggle" data-target="pwd_confirm"><i class="fas fa-eye"></i></button>
                        </div>
                        <div style="display:flex; align-items:center; justify-content:flex-end;">
                            <button type="submit" class="btn-primary" style="background:#ec343c;">Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
            <script>
            (function(){
              var t = document.getElementById('toggle_pwd');
              var f = document.getElementById('pwd_form');
              if(t && f){
                t.addEventListener('click', function(){
                  var s = window.getComputedStyle(f).display;
                  f.style.display = (s === 'none') ? 'block' : 'none';
                });
              }
              var toggles = document.querySelectorAll('.eye-toggle');
              toggles.forEach(function(btn){
                btn.addEventListener('click', function(){
                  var id = this.getAttribute('data-target');
                  var input = document.getElementById(id);
                  if(!input) return;
                  if(input.type === 'password'){
                    input.type = 'text';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i>';
                  } else {
                    input.type = 'password';
                    this.innerHTML = '<i class="fas fa-eye"></i>';
                  }
                });
              });
            })();
            </script>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>
