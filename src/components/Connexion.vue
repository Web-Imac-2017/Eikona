<template lang="html">
  <form>
    <button @click="connexion">Connexion</button>
    <button @click="inscription">Inscription</button>
  </form>
</template>

<script>
export default {
  methods : {
    connexion () {
      console.log("connexion");
    },
    inscription () {
      console.log("inscription");
    }
  }
};
</script>

<style lang="css" scoped>
  form > * {
    display : inline-block;
  }
</style>
