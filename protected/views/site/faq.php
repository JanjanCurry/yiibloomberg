<div class="content-header wow slideInDown">
    <div class="container">
        <h3>FAQ</h3>
    </div>
</div>

<div class="container">

    <div class="panel-group" id="accordion">
    <?php
    $faqs = array(
        'What makes Complete Intelligence the best choice?' => 'Complete Intelligence is focused on relevant, high-quality forecast data for corporates, governments and small / mid-sized firms. Our data is collected, cleaned, and forecast through a statistically-valid process to help our clients make informed and timely decisions. Our interface is clean, clear and concise. We don\'t bog our customers down with unnecessarily confusing interfaces, ill-informed or overly-wordy narratives, or hard-coded "forecasts" that are manually adjusted so they\'re not too far off of the "consensus number."<br /></br />We have a real model. We do real forecasts. And we want to help you make better decisions. That\'s it.',

        'How do I subscribe?' => 'Subscriptions are available on our website. Product descriptions and pricing are available <a href="https://www.completeintel.com/index.php/contact/">here</a>',

        'I can\'t log in or have lost my password. How can I reset it or get it again?' => 'On the login page, please select "Forgot password". We will send you a recovery email.',

        'I have a billing issue. How can I get it resolved?' => 'Please log into your account and contact customer service <a href="https://www.completeintel.com/index.php/contact/">here</a>',

        'How often are fees billed?' => 'We invoice customers in advance on a monthly basis, unless there is an annual subscription, in which case fees are paid at the start of the subscription period.',

        'What is your return policy?' => 'Please see Section 21 of our Terms of Use regarding termination of your account: <a href="https://www.completeintel.com/index.php/terms-of-use/">Terms of Use</a>',

        'How can I report a technical issue?' => 'We take technical issues seriously and want to address them as quickly as possible. If you have a technical issue, please notify our customer service team right away <a href="https://www.completeintel.com/index.php/contact/">here</a>.',

        'Can I get a free trial?' => 'We offer a free access through our freemium subscription. This is actual data with limited functionality for a small set of countries. Here\'s where you can <a href="https://www.completeintel.com/index.php/contact/">sign up</a>',

        'Do you have a downloadable software?' => 'All of our data is available through an Internet browser.',

        'What are your sources of information?' => 'Our data sources are largely open sources: multilateral agencies, national statistical agencies, market and exchange closing data, and industry associations.',

        'How often do you update data?' => 'We reforecast all of our data once a month. This is a complete reforecasting process, wherein we reexamine and reconfigure our algorithms for every data series on a monthly basis to reflect the very small and incremental changes that occur in the world economy and markets each month.',

        //'Why haven\'t you included Taiwan?' => 'We love Taiwan, but we don\'t include it in our country data at the moment. This is not a political statement and we hope it isn\'t read that way. This is simply a matter of source data and utilizing sources for Taiwan data that are comparable to our other country data. Once we can do this, we will definitely include Taiwan.',

        'Are US dollar values current or constant?' => 'Values are current US dollar values for the year reported. The value is calculated by UN Comtrade by weighting the monthly exchange rate with the monthly volume of trade, then converting from local currency units to US dollars.',

        'Why don\'t the sum of countries for some indicators add up to world totals?' => 'Already existing - no text change only number reordering.',

        'Why don\'t the sum of countries for some indicators add up to world totals?' => 'In many cases, world totals will not add up to the sum of the 95 countries or their 195 trade partners that Complete Intelligence tracks. In many cases, as with trade, for example, the difference is made up by non-sovereign trade partners (territories or other possessions) that may not be tracked in national statistics. In many cases, Complete Intelligence does track these non-sovereign territories, so we can supply this information on a customized basis. Please contact our customer support for inquires.',

        'Are country names consistent across all of your data sources?' => 'Multilateral organizations may track some nations in ways that may be confusing for users unfamiliar with the individual methodologies. Norway, for example, is tracked by the United Nations as "Norway, Svalbard and Jan Mayen". Switzerland is tracked by the UN as "Switzerland, Liechtenstein". In these cases, we have simplified to "Norway" and "Switzerland" and may do so for other countries where no other simplified option is available.',

        'Can I invest using your forecasts?' => 'Our forecasts are not investment advice. These are statistical forecasts based on the relationships between various items in the global economy. Please don\'t use this to invest.',

        'Why don\'t you provide commentary around your forecasts?' => 'We are a data firm. There is a lot of economic and industry commentary available <a href="https://www.bloomberg.com/view/">Bloomberg</a>, <a href="http://www.cnbc.com/commentary/">CNBC</a>, <a href="http://www.reuters.com/commentary/">Reuters</a>, <a href="https://finance.yahoo.com/">Yahoo</a>, <a href="https://www.google.com/finance">Google</a>, <a href="https://www.imf.org/en/data/imf-finances">IMF</a>, and <a href="https://www.worldbank.org">World Bank</a>. To be honest, we don\'t believe more reading is what you need. We believe you need better data.',

        'Do you provide more detailed forecasts or customized forecasts?' => 'Yes! However, these are fee based, so if you would like to ask about custom forecasts, please don\'t send an email from a generic service (Google, Hotmail, etc). We\'ll need to see corporate or organizational email ID. Please let us know how we can help you <a href="http://www.completeintel.com/index.php/contact/">here</a>.',

        'Do you provide data feeds and retained services for recurring customized forecasts?' => 'Of course we do. Just let us know the data series and countries you would like as well as the format you prefer. Contact us <a href="http://www.completeintel.com/index.php/contact/">here</a> and let us know the name of your organization, how you will use the data, and how we can contact you (no Google, Hotmail or generic email IDs, please).',

        'Do you work with academics and students?' => 'We love students. We would be happy to come to blanket subscription agreements with your class or your university, so please contact us <a href="http://www.completeintel.com/index.php/contact/">here</a>.',

        'Do you have corporate subscriptions?' => 'Absolutely yes! Please contact us <a href="http://www.completeintel.com/index.php/contact/">here</a> and let us know the name of your organization, the number of users and how we can contact you (no Google, Hotmail or generic email IDs, please).',

        'Do you have a mobile app?' => 'We don\'t have a mobile app, but our website is optimized for mobile.',

        'Can I private label your data?' => 'Redistribution and sublicensing of Complete Intelligence data is not covered by our standard terms of use. If your firm is interested in redistributing or sublicensing our data, please contact our customer service team <a href="https://www.completeintel.com/index.php/contact/">here</a>.',

        'Do you provide APIs, enterprise subscriptions or discount for groups?' => 'Yes. For 5 or more accounts, we will provide enterprise subscriptions or APIs. Please contact our customer service <a href="https://www.completeintel.com/index.php/contact/">here</a> to discuss. ',

        'From where does Complete Intelligence get its data?' => 'We get our data from open sources, including multilateral institutions and government statistical agencies, and market data from exchanges. We don\'t use proprietary data sources.',

        'Can I get raw economics and trade data?' => 'Yes, you can access the data and export it in Excel-friendly CSV format.',

        'Do you offer discounts?' => 'Yes! We offer academic discounts and multi-year subscription discounts. Contact a Complete Intelligence Specialist to learn more.',

        'Can I upgrade my plan at any time?' =>  'Of course! If you\'re a Freemium subscriber, click "Upgrade" at any time. If you want to upgrade to Enterprise, please contact us, and a Complete Intelligence specialist can help find the right fit for you.',

        'What types of payment do you accept?' => 'We accept all major credit cards.',

        'What is your cancellation policy?' => 'For first-time Complete Intelligence subscribers, we offer a 24-hour money back guarantee, as long as no data has been downloaded from the Complete Intelligence platform. You can cancel your account by contacting a customer service representative. After the 24-hour period, canceling prevents your account from renewing, but you will continue to have access to your subscription through the end of the paid period. See our terms of service and payment terms.',

        'Commodity Prices - What price basis do you use?' => 'Our commodity prices are based on futures contract pricing. In order to account for all active futures contracts, we calculate a “continuous futures” price, which is generally similar to the way stock splits are handled for equity market calculations.',

        'What is a Continuous Future?' => 'The approach we use to calculate continuous futures is called “Backwards Ratio Adjusted Prices, Roll On Last Trading Day”. Price histories of each underlying contract are multiplied by a constant amount, starting with the newest contract and working backwards. The intention with this approach is to eliminate jumps in price between consecutive contracts. On every roll date, the ratio between the back contract\'s settle price and the front contract\'s settle price (back settle divided by front settle) is computed. The entire historical series is then multiplied by this ratio, adjusting the full contract history on every roll date. To avoid biases when using the Ratio method, Profit/Loss should be calculated based on price percentage changes. Consistent contango or backwardation can lead to very large or very small absolute magnitudes for historical prices, but percentage-based PL calculations should mitigate aberrant magnitudes.',

        'What is r-squared?' => 'R-squared tells the user how well the forecast represents the historical behavior of the data. It represents how closely the data fit the forecast line. The lower the r-squared, the worse the fit. The higher, the better. So an r-squared of 1 is a perfect fit.',

        'What is a correlation?' => 'The correlation listed next to each asset represents the relationship of that particular asset with the economic indicators that we test as outlined in our methodology. It works as an indicator to the certainty of our forecasts. The range varies from -1 to +1. So, +1 is a perfect fit, -1 is a perfect inverse fit and 0 is not a fit. The closer it is to 0, the lower the ability to forecast accurately. Correlation only identifies possible connections between variables; it does not prove or disprove any causal relationships.',

        'What is our Confidence indicator?' => 'We indicate red, amber and green for low, average and high levels of confidence, respectively. These levels are based on the statistical correlations as outlined below - High: 0.851-0.99 | Mid: 0.70-0.85 | Low: <0.70',
    );


    $i = 0;
    $slide = 'Left';
    foreach($faqs as $que => $ans){
        $i++;
        ?>

        <div class="panel panel-default wow slideInUp">
            <div class="panel-heading">
                <h5 class="panel-title">
                    <a class="accordion-toggle l-fw" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $i; ?>"><?php echo $i.'. '.$que; ?></a>
                </h5>
            </div>
            <div id="collapse<?php echo $i; ?>" class="panel-collapse collapse">
                <div class="panel-body">
                    <p><?php echo $ans; ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>
