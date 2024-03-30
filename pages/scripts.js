///////////////////////////////////////////////////////////////
///////////////// FONCTIONS PAGE D'ACCUEIL ////////////////////
//////////////////////////////////////////////////////////////
function logout() {
    window.location.href = "./logout.php";
}

function accueil_click() {
    window.location.href = "./index.php";
}

function account_click() {
    window.location.href = "./account.php";
}

function search_click() {
    window.location.href = "./recherche.php";
}

function publications_click() {
  window.location.href = "./publications.php";
}

function settings_click() {
    window.location.href = "./settings.php";
}

function arrobase_preremplie() {
  var usernameInput = document.getElementById('username');

  usernameInput.addEventListener('input', function() {
    if (!usernameInput.value.startsWith('@')) {
      usernameInput.value = '@' + usernameInput.value;
    }
  });
}

function removeAtSymbol() {
  var usernameInput = document.getElementById('username');
  if (usernameInput.value.startsWith('@')) {
    usernameInput.value = usernameInput.value.substring(1);
  }
  return true;
}

function closeErrorMessageLogin() {
    document.getElementById('error-message').style.display = 'none';
    document.getElementById('overlay').classList.remove('visible');
}

document.addEventListener('click', function (event) {
    var overlay = document.getElementById('overlay');
    if (event.target === overlay) {
        closeErrorMessageLogin();
    }
});


///////////////////////////////////////////////////////////////
///////////////// FONCTION BARRE D'EMOJIS /////////////////////
//////////////////////////////////////////////////////////////
function setupEmojiSelector() {
  const emojiData = ['😭','😂','❤️','👍','🔥','🚀','🙏','👉','🔴','🚨'];
  const emojiSelector = document.querySelector('.emoji'); 
  const emojiList = document.createElement('div');
  emojiList.id = 'emojiList';
  emojiList.style.display = 'none';
  emojiList.style.position = 'absolute';
  emojiList.style.backgroundColor = '#fff';
  emojiList.style.border = '1px solid #ccc';
  emojiList.style.borderRadius= "20px";
  emojiList.style.padding = '5px';

  // position a droite de la liste
  const emojiSelectorRect = emojiSelector.getBoundingClientRect();
  emojiList.style.left = emojiSelectorRect.right + 'px';
  emojiList.style.top = emojiSelectorRect.top + 'px';

  // creation liste
  emojiData.forEach(emoji => {
    const emojiItem = document.createElement('span');
    emojiItem.innerText = emoji;
    emojiItem.style.marginRight = '5px';
    emojiItem.style.cursor = 'pointer';
    emojiItem.addEventListener('click', () => addEmojiToTextarea(emoji));
    emojiList.appendChild(emojiItem);
  });

  emojiSelector.addEventListener('click', toggleEmojiList);
  document.body.appendChild(emojiList);
  
  function toggleEmojiList() {
    emojiList.style.display = (emojiList.style.display === 'block') ? 'none' : 'block';
  }

  // afficher emoji dans textarea
  function addEmojiToTextarea(emoji) {
    const textarea = document.getElementById('message');
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    textarea.value = textBefore + emoji + textAfter;
    
    const newCursorPos = cursorPos + emoji.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
  }
  emojiList.style.userSelect = 'none';
}



function chargerImagePubli() {
    const inputImage = document.createElement('input');
    inputImage.type = 'file';
    inputImage.accept = 'image/*';

    inputImage.addEventListener('change', async function () {
        const file = inputImage.files[0];

        if (file) {
            const formData = new FormData();
            formData.append('image', file);

            try {
                const response = await fetch('./controleur/charger_image.php', {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    const imageUrl = await response.text();
                    const messageTextarea = document.getElementById('message2');
                    messageTextarea.value += `<br><a href="./${imageUrl}" target="_blank"><img src="./${imageUrl}" alt="uploaded image" /></a>`;
                } else {
                    console.error('Erreur lors du téléversement de l\'image.', response.statusText);
                }
            } catch (error) {
                console.error('Erreur lors du téléversement de l\'image.', error);
            }
        }
    });

    inputImage.click();
}



///////////////////////////////////////////////////////////////
///////////////// MODIFICATIONS DE LA BD /////////////////////
//////////////////////////////////////////////////////////////
function edit_nom_utilisateur() {
  var nouveauNomUtilisateur = prompt("Nouveau nom d'utilisateur :");
  if (nouveauNomUtilisateur !== null && nouveauNomUtilisateur.trim() !== "") {
    editInfoCoteServeur('edit_nom_utilisateur', nouveauNomUtilisateur);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function edit_prenom() {
  var nouveauPrenom = prompt("Nouveau prénom :");
  if (nouveauPrenom !== null && nouveauPrenom.trim() !== "") {
    editInfoCoteServeur('edit_prenom', nouveauPrenom);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function edit_nom() {
  var nouveauNom = prompt("Nouveau nom :");
  if (nouveauNom !== null && nouveauNom.trim() !== "") {
    editInfoCoteServeur('edit_nom', nouveauNom);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function edit_bio() {
  var nouvelleBio = prompt("Nouvelle bio :");
  if (nouvelleBio !== null && nouvelleBio.trim() !== "") {
    editInfoCoteServeur('edit_bio', nouvelleBio);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function edit_tel() {
  var nouveauTel = prompt("Nouveau Tel :");
  if (nouveauTel !== null && nouveauTel.trim() !== "") {
    editInfoCoteServeur('edit_tel', nouveauTel);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function edit_mail() {
  var nouveauMail = prompt("Nouveau mail :");
  if (nouveauMail !== null && nouveauMail.trim() !== "") {
    editInfoCoteServeur('edit_mail', nouveauMail);
  }
  setTimeout(function() {
      location.reload();
  }, 500);
}

function editInfoCoteServeur(action, nouvelleValeur) {
  var url = "./controleur/modifier_compte.php";
  var params = new URLSearchParams();
  params.append("action", action);
  params.append("nouvelle_valeur", nouvelleValeur);

  fetch(url, {
    method: "POST",
    headers: {
      "Content-type": "application/x-www-form-urlencoded"
    },
    body: params
  })
  .then(response => {
    if (!response.ok) {
      throw new Error("Erreur de réponse");
    }
    return response.text();
  })
  .then(data => {
    console.log(data);
  })
  .catch(error => {
    console.error("Erreur de fetch :", error);
  });
}

function delete_post(element) {
    var form = element.closest('.delete-post-form');
    form.submit();
}

///////////////////////////////////////////////////////////////
///////////////// POPUS ABONNES ABONNEM /////////////////////
//////////////////////////////////////////////////////////////
function afficherPopupAbo() {
    var popup = document.getElementById("popup");
    var overlay = document.getElementById("overlay");
    popup.style.display = "block";
    overlay.classList.add('visible');
}

function fermerPopupAbo() {
    var popup = document.getElementById("popup");
    var overlay = document.getElementById("overlay");
    popup.style.display = "none";
    overlay.classList.remove('visible');
    document.addEventListener('click', function (event) {
        var overlay = document.getElementById('overlay');
        if (event.target === overlay) {
            fermerPopupAbo();
        }
    });
}


async function gererAbonnement(idUtilisateurCible) {
    try {
        const response = await fetch('./controleur/traitement_abo_desabo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'id_utilisateur': idUtilisateurCible,
            }),
        });

        if (!response.ok) {
            throw new Error('Erreur de réponse');
        }

        const result = await response.json();
        // Affiche la réponse dans la console (utile pour le débogage)
        console.log(result);
        // Attend 200 millisecondes avant de recharger la page
      
        setTimeout(() => {
            location.reload();
        }, 100);
    } catch (error) {
        console.error('Erreur de fetch :', error);
    }
}


async function toggleLike(postId, action) {
    try {
        const response = await fetch('./controleur/traitement_likes_dislikes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ postId, action }),
        });

        if (response.ok) {
            const likesData = await response.json();
            updateLikes(postId, likesData);
        } else {
            console.error('Erreur lors de la requête AJAX. Statut :', response.status);
            console.error('Réponse serveur :', await response.text());
        }
    } catch (error) {
        console.error('Erreur lors de la requête AJAX', error);
    }
}



function updateLikes(postId, likesData) {
    const likeCountElement = document.getElementById(`likeCount_${postId}`);
    const dislikeCountElement = document.getElementById(`dislikeCount_${postId}`);

    if (likeCountElement && dislikeCountElement) {
        likeCountElement.textContent = likesData.totalLikes;
        dislikeCountElement.textContent = likesData.totalDislikes;
    } else {
        console.error(`Éléments non trouvés dans le DOM pour postId : ${postId}`);
    }
}


function commenter(idPublication) {
    const contenuCommentaire = document.getElementById(`commentaire_${idPublication}`).value;

    if (contenuCommentaire) {
        fetch('./controleur/traitement_commentaires.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_publication=${idPublication}&commentaire=${encodeURIComponent(contenuCommentaire)}`,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP! Statut : ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Rafraîchit la page pour afficher le nouveau commentaire
                location.reload();
            } else {
                alert('Erreur lors de l\'envoi du commentaire. Message : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur Fetch:', error);
        });
    }
}

function toggleCommentForm(publicationId) {
    var commentForm = document.getElementById('commentForm_' + publicationId);
    if (commentForm.style.display === 'none' || commentForm.style.display === '') {
        commentForm.style.display = 'block';
    } else {
        commentForm.style.display = 'none';
    }
}

function toggleAllComments(id) {
    var commentsContainer = $("#commentsContainer_" + id);
    var toggleButton = $("#toggleComments_" + id);

    if (commentsContainer.hasClass("all-comments-visible")) {
        commentsContainer.removeClass("all-comments-visible");
        toggleButton.text("Afficher tous");
    } else {
        commentsContainer.addClass("all-comments-visible");
        toggleButton.text("Afficher moins");
    }
}


function deleteComment(commentId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
        fetch('./controleur/supprimer_comm.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `commentId=${commentId}`,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP! Statut : ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                // Rafraîchit la page pour refléter la suppression du commentaire
                location.reload();
            } else {
                alert('Erreur lors de la suppression du commentaire. Message : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur Fetch:', error);
        });
    }
}


function chargerPlusDeCommentaires(idPublication) {
    $.ajax({
        url: './controleur/chargement_commentaires.php',
        type: 'POST',
        data: { id_publication: idPublication },
        success: function (data) {
            $('#commentairesDiv_' + idPublication).append(data);
            if (data.trim() === '') {
                $('.voir-plus').hide();
            }
        },
        error: function () {
            console.log('Erreur lors du chargement des commentaires.');
        }
    });
}