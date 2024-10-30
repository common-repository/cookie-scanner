<div class="wrap">

<h1><?php echo $settings_object->settings_page_configs->page_title ?></h1>
<h2>Please get a free license first.</h2>
<p>Cookie Scanner can crawl your page and search for your cookies in a chrome browser and inserts them automatically to wordpress.
    From there you can display your cookies with a shortcode for example in your data privacy statement.
    You can insert your cookies manually without a license, as well. But then there won't be an alerting, if a new cookie pops up.</p>
    <p style="font-weight:bold;">No worries: A basic license is free.</p>
    <p style="font-weight:500;">Why should I get a license?</p>
<p>Then cookies are inserted automatically and you can get a notice if new cookies appear on your page.
</p>
<p style="font-weight:500;">Why do I have to get a license?</p>
<p>The crawler runs on an external server provided by the author.
    And every single crawl cost server resources which need to be paid.
</p>
<p style="font-weight:500;">How many crawls can I do with a free license?</p>
<p>In general: crawling one page per month, and new licenses get an extra budget of 5 crawls.
</p>
<p style="font-weight:500;">I want more crawls, how can I get them?</p>
<p>At the moment you have to contact me directly, please sent me a private message: support@cookie-scanner.com
</p>
<p style="font-weight:500;">What data is submitted if i get a license?</p>
    <p>for license creation:</p>
    <ul style="list-style: square;position:relative;left:2.4em;">
        <li>url of your page</li>
        <li>e-mail of user who accepts terms and creates license</li>
        <li>version of accepted terms</li>
        <li>a unique id for the license</li>
    </ul>
    <p>for a crawling request:</p>
    <ul style="list-style: square;position:relative;left:2.4em;">
        <li>Urls of pages to crawl</li>
        <li>license key</li>
    </ul>
</p>
    <strong>Disclaimer:</strong> this services uses puppeteer for crawling to mimic user behaviour, but we can not gurantee to find all cookies on your page. You should not rely on this service.
    <form method="POST">
        <input type="checkbox" name="nscs_acceptTermsCookieScanner" value="1">I agree to the <a target="_blank" href="https://cookie-scanner.com/docs/terms/v1/terms.html">terms
      and conditions</a>,accept that the services comes without any guarantees and liabilities and the <a target="_blank" href="https://cookie-scanner.com/docs/terms/v1/data-privacy.html">data privacy</a><br>
        <br><input class="button button-primary" type="submit" value="Accept and create a free license">
    </form>
</p>
