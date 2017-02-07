# La base de donnée
## Les paramètres
La table PARAMS contient les paramètre globaux du site. Indiquez ici les paramètre que vous utilisez/ajoutez afin que tout soit centraliser.

|  PARAM_NAME   |  PARAM_VALUE  | Description |
| ------------- | ------------- | ----------- |
| NBR_PROFILES_MAX | 20  | Nombre maximum de profils que peut possèder un compte |

## Détails des tables
Les champs marqués en tant que *tinyint* ou *boolean* sont là pour être utilisé comme des boolean avec **1** pour vrai et **0** pour faux.

### Users

|    Colonne    | Description |
| ------------- | ----------- |
| user_id |  |
| user_name |  |
| user_email |  |
| user_password |  |
| user_register_time |  |
| user_last_activity |  |
| user_moderator | 0: Pas modérateur; 1: Modérateur |
| user_admin | 0: Pas administrateur; 1: Administrateur |
| user_activated | 0: Utilisateur n'a pas validé l'email d'activation; 1: utilisateur activé |