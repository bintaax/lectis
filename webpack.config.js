const Encore = require('@symfony/webpack-encore');

// Initialise l'environnement Encore quand ce fichier est lance en dehors de la commande habituelle.
// Cela permet aux outils annexes de reutiliser cette configuration sans erreur.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
      .enablePostCssLoader()
    // Definis le dossier dans lequel les assets compiles seront ecrits.
    .setOutputPath('public/build/')
    // Definis l'URL publique utilisee par le navigateur pour charger ces assets.
    .setPublicPath('/build')
    // A activer uniquement si les assets sont servis par un CDN ou un sous-dossier.
    //.setManifestKeyPrefix('build/')

    /*
     * Configure les points d'entree compiles par Encore.
     * Chaque entree genere un fichier JavaScript et, si besoin, un fichier CSS associe.
     */
    .addEntry('app', './public/assets/js/script.js')

    // Decoupe le bundle en plusieurs morceaux pour ameliorer le chargement et le cache.
    .splitEntryChunks()

    // Active le runtime separe, utile pour la plupart des applications multi-pages.
    .enableSingleRuntimeChunk()

    /*
     * Active ici les options de build complementaires selon les besoins du projet.
     * La documentation Symfony detaille toutes les possibilites disponibles.
     */
    .cleanupOutputBeforeBuild()

    // Peut afficher des notifications systeme pendant les builds locaux.
    // .enableBuildNotifications()

    .enableSourceMaps(!Encore.isProduction())
    // Ajoute un hash au nom des fichiers en production pour mieux gerer le cache navigateur.
    .enableVersioning(Encore.isProduction())

    // Permet d'ajouter des plugins Babel supplementaires si le projet en a besoin.
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // Configure les polyfills automatiquement selon le code reellement utilise.
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // Active le support Sass/SCSS si le projet en a besoin plus tard.
    //.enableSassLoader()

    // A decommenter si le projet adopte TypeScript.
    //.enableTypeScriptLoader()

    // A decommenter si une partie du front passe sur React.
    //.enableReactPreset()

    // A decommenter pour ajouter des attributs d'integrite sur les assets servis.
    //.enableIntegrityHashes(Encore.isProduction())

    // Peut injecter jQuery automatiquement pour les plugins legacy qui l'attendent.
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
