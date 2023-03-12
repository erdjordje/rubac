Setup
------------
Run docker container

<pre><code>docker compose up -d
</code></pre>

Run following test to check configuration.

<pre><code>docker exec rubac php vendor/bin/phpunit --bootstrap vendor/autoload.php --no-configuration tests/RuBACTest.php
</code></pre>
