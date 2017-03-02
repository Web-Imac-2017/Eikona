# Bloquage

## Bloquage d'un compte
Pour bloquer un compte, il faut utiliser l'url suivante.

* **blocker_id** : ID du compte bloquant
* **blocked_id** : ID du compte bloqué

### URL
```
/block/<blocker_id>/<blocked_id>
```

## Debloquage d'un compte

Pour débloquer un compte, il faut utiliser l'url suivante.

* **blocker_id** : ID du compte bloquant
* **blocked_id** : ID du compte bloqué

### URL
```
/unblock/<blocker_id>/<blocked_id>
```

## Savoir si un compte bloque un autre

Pour savoir si un compte bloque un autre compte, il faut utiliser l'url suivante.

* **blocker_id** : ID du compte bloquant
* **blocked_id** : ID du compte bloqué

### URL
```
/isblocking/<blocker_id>/<blocked_id>
```
