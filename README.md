# MonApiBundle

# Requis :
-STOF avec Sluggable configuré.<br>
-Symfony (3.1.X). <br>

# Installation :
-Ajouter le Bundle dans le dossier Vendor.<br>
-Editer les lignes :<br>
<pre><code>
(app/AppKernel.php)
    new MonApiBundle\MonApiBundle(),
    
(app/config/config.yml)
    - { resource: "@MonApiBundle/Resources/config/services.yml" }
    
(app/config/routing.yml)
    mon_api:
        resource: "@MonApiBundle/Resources/config/routing.yml"
        prefix:   /
</code></pre>
Installer les bases de données.<br>
<pre><code>php bin/console dotrine:schema:update --force</code></pre>
Ouvrir le dossier SQL et injecter les données (SQL/ville_france).<br>
Installer les assets.<br>
<pre><code>php bin/console asset:install --symlink</code><pre>

