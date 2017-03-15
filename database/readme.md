# La base de données

## Installation

La création de la structure se fait par le biais des requêtes contenus dans le fichier `DB CREAT.sql`.
Il est possible de remplir une partie des tables avec des données déjà générés, en important le fichier `DB POPULATE.sqm`.

**Important :** Il est fortement recommander de désactiver la vérification des clés étrangères lors de l'exécution des fichiers `DB *`.


### Les paramètres
La table PARAMS contient les paramètre globaux du site. Indiquez ici les paramètre que vous utilisez/ajoutez afin que tout soit centraliser.

|  PARAM_NAME   |  PARAM_VALUE  | Description |
| ------------- | ------------- | ----------- |
| NBR_PROFILES_MAX | 3  | Nombre maximum de profils que peut possèder un compte |

## Détails des tables

Détails de toutes les tables, et de leur strcture. Les possédant des noms ambigus sont détaillés.

Les champs marqués en tant que *tinyint* ou *boolean* sont là pour être utilisé comme des boolean avec **1** pour vrai et **0** pour faux.

### banned_emails

|    Colonne         | Description |
| ------------------ | ----------- |
| banned_email       |  |

### banned_words

|    Colonne         | Description |
| ------------------ | ----------- |
| word               |  |

### blocked

|    Colonne         | Description |
| ------------------ | ----------- |
| blocker_id         | Utilisateur qui bloque |
| blocked_id         | Utilisateur qui se fait bloquer |
| block_time         |  |

### comment_likes

|    Colonne         | Description |
| ------------------ | ----------- |
| profile_id         | Profile qui aime le commentaire |
| comment_id         | Commentaire aimé |
| like_time          |  |

### comments

|    Colonne         | Description |
| ------------------ | ----------- |
| comment_id         |  |
| profile_id         |  |
| post_id            |  |
| comment_text       |  |
| comment_time       |  |

### followings

|    Colonne         | Description |
| ------------------ | ----------- |
| follower_id        | Profile qui suit |
| followed_id        | Profile qui se fait suivre |
| following_time     |  |
| follower_subscription | 0: Abonnement simple; 1: Le follower recoit une notification a chaque nouveau post du followed |
| follow_confirmed   | Si le profil suivie est privé, celui-ci doit confirmer l'abonnement. |

### notifications

|    Colonne         | Description |
| ------------------ | ----------- |
| notif_id           |  |
| profile_target_id  | Profile qui recoit la notification |
| notif_type         | Type de la notification, à définir |
| notif_target       | Vers quoi la notification redirige |
| notif_time         |  |
| notif_seen         | 0: Notification pas vue; 1: La notification à été vue|
| profile_id         | Le profil emmeteur de la notification |

### PARAMS

|    Colonne         | Description |
| ------------------ | ----------- |
| PARAM_NAME         |  |
| PARAM_VALUE        |  |
| PARAM_edit_time    | Date de la dernirèe édition du paramètre |
| PARAM_edit_user_id | Dernier utilsiateur qui à modifié le paramètre |

### post_likes

|    Colonne         | Description |
| ------------------ | ----------- |
| profile_id         | Profile qui aime le post |
| post_id            | Post aimé |
| like_time          |  |

### posts

|    Colonne         | Description |
| ------------------ | ----------- |
| post_id            |  |
| profile_id         |  |
| post_type          | "photo" ou "video" |
| post_extension     | "png", "jpg", etc. |
| post_description   |  |
| post_publish_time  |  |
| post_edit_time     |  |
| post_state         | 1: Normal; 0: Caché, 2: En modération  |
| post_filter        | Filtre utilisé sur le post |
| post_geo_lat       | Lattitude si la position est renseignée |
| post_geo_lng       | Longitude si la position est renseignée |
| post_geo_name      | Nom du lieu si la position est renseignée |
| post_allow_comment | 0: Le post ne peut pas être commenté; 1: Le post peut être commenté |
| post_approved      | 0: Le post n'a pas encore été apprové; 1: Le post n'a pas encore été approuvé |

### post_views

|    Colonne         | Description |
| ------------------ | ----------- |
| profile_id         | Le profile qui aime le post |
| post_id            | Le post qui est aimé |
| view_time          |  |

### profiles

|    Colonne         | Description |
| ------------------ | ----------- |
| profile_id         |  |
| user_id            |  |
| profile_name       |  |
| profile_desc       |  |
| profile_create_time |  |
| profile_views      |  |
| profile_private    | 0: Profile public (pas privé); 1: Profile privé |
| profile_picture    | |
| profile_key        | Unique key of the user, to prevent folder browsing |

### reports

|    Colonne         | Description |
| ------------------ | ----------- |
| report_id          |  |
| user_id            | Utilisateur qui signale |
| post_id            | Post qui est signalé |
| report_comment     | Commentaire de l'utilisateur |
| report_status      | État du signalement: 0: En attente; 1: Pris en charge; 2: Clos |
| report_handler     | Modérateur en charge du signalement |
| report_result      | Résultat du signalement: 0: En attente; 1:Rapport ignoré; Le reste à définir |
| time_state_change  | Dernière mise à jour du signalement |

### tags

|    Colonne         | Description |
| ------------------ | ----------- |
| tag_name           | Mot-clé utilisé |
| post_id            | Post ou le mot-clé est utilisé |
| use_time           |  |

### users

|    Colonne         | Description |
| ------------------ | ----------- |
| user_id            |  |
| user_name          |  |
| user_email         |  |
| user_password      |  |
| user_register_time |  |
| user_last_activity |  |
| user_moderator     | 0: Pas modérateur; 1: Modérateur |
| user_admin         | 0: Pas administrateur; 1: Administrateur |
| user_activated     | 0: Utilisateur n'a pas validé l'email d'activation; 1: utilisateur activé |
| user_code          | Utilisé quand l'utilisateur perd son mot de passe |
| user_key           | Clé unique de l'utilisateur |