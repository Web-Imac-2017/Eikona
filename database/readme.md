# La base de donnée
## Les paramètres
La table PARAMS contient les paramètre globaux du site. Indiquez ici les paramètre que vous utilisez/ajoutez afin que tout soit centraliser.

|  PARAM_NAME   |  PARAM_VALUE  | Description |
| ------------- | ------------- | ----------- |
| NBR_PROFILES_MAX | 20  | Nombre maximum de profils que peut possèder un compte |

## Détails des tables
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
| blocker_id         | user_id of the blocker |
| blocked_id         | user_id of the person beiing blocked |
| block_time         |  |

### comment_likes

|    Colonne         | Description |
| ------------------ | ----------- |
| profile_id         | Profile who likes the comment |
| comment_id         | Comment being liked |
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
| follower_id        | Profile who follow |
| followed_id        | Profile who is being followed |
| following_time     |  |
| follower_subscription | 0: The profile is just following someone; 1: The profile wants to receive notifications for every post |

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

### profiles

|    Colonne          | Description |
| ------------------- | ----------- |
| profile_id          |  |
| user_id             |  |
| profile_name        |  |
| profile_desc        |  |
| profile_create_time |  |
| profile_views       |  |
| profile_private     | 0: Profile public (pas privé); 1: Profile privé |