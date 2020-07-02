$(function(){

    $('.advanced-search').data('url', []);

    var $av = $('.advanced-search');

    $('.search-list-nav-btn').click(function(){

        var target = $(this).data('list-target');
            $group = $(this).parents('.search-list-nav'),
            $list = $('.search-list-groups .search-list[data-list="'+target+'"]');

        $('.advanced-search').data('active', target);

        $group.find('.btn').removeClass('active');
        $(this).addClass('active');

        toggleSearchNav();

        $('.search-list-groups .search-list:not([data-list="'+target+'"])').slideUp();
        $('.search-list-headers').html('');
        if($list.length > 0){
            var type = $av.data('active'),
                filter = {
                    type: target,
                };
            switch(type){
                case 'reporter-macro':
                    filter['type'] = 'reporter-macro';
                    filterLists($list, filter, 'list');
                    break;

                case 'reporter-trade':
                    filter['type'] = 'reporter-trade';
                    filterLists($list, filter, 'list');
                    break;

                case 'macro':
                    filter['type'] = 'macro';
                    filter['returnPartner'] = true;
                    filterLists($list, filter, 'list');
                    break;
            }

            $('.search-list-headers').html($list.find('.search-list-header').html());

            $list.slideDown();

            $av.data('url', []);
            $('.search-list-groups .search-list').removeClass('selected').find('.search-list-selected').data('ref','').text('None');
        }
        $('.search-submit').slideUp();
    });

    $('.search-list .list-group-item').click(function(){
        if(!$(this).hasClass('list-group-item-disabled')) {
            var $list = $(this).parents('.search-list'),
                complete = true;

            $av.data('url').push({
                name: $list.data('list'),
                val: $(this).data('ref')
            });
            $list.slideUp();
            $list.find('.search-list-selected').data('ref', $(this).data('ref')).text($(this).data('label'));

            if ($list.data('list') === $av.data('active')) {
                switch ($av.data('active')) {
                    case 'macro':
                        var $target = $('.search-list-groups .search-list[data-list="reporter-macro"]');
                        filterLists($target, {
                            type: 'macro',
                            macro: $(this).data('ref'),
                        }, 'list');
                        $target.slideDown();
                        complete = false;
                        break;

                    case 'trade':
                        var $target = $('.search-list-groups .search-list[data-list="reporter-trade"]');
                        filterLists($target, {
                            type: 'trade',
                            indicator: $(this).data('ref')
                        }, 'list');
                        $target.slideDown();
                        complete = false;
                        break;

                    case 'reporter-macro':
                        var $target = $('.search-list-groups .search-list[data-list="macro"]');
                        filterLists($target, {
                            type: 'macro',
                            reporter: $(this).data('ref'),
                            returnPartner: true
                        }, 'list');
                        $target.slideDown();
                        complete = false;
                        break;

                    case 'reporter-trade':
                        var $target = $('.search-list-groups .search-list[data-list="trade"]');
                        $target.slideDown();
                        complete = false;
                        break;
                }

                $('.search-list-headers').html($list.find('.search-list-header').html());
            } else {
                $('.search-list-headers').append($list.find('.search-list-header').html());
            }
            if (complete) {
                $('.search-submit').slideDown();
            } else {
                $('.search-submit').slideUp();
            }
        }
    });


    $('.search-submit-btn').click(function(){
        var parts = $av.data('url'),
            urlData = {};
        for(var key in parts){
            urlData[parts[key].name] = parts[key].val
        }

        var url = '';
        switch($av.data('active')){
            case 'reporter-macro':
            case 'macro':
                url = '/economics/index/macro-'+urlData['macro']+'/reporter-'+urlData['reporter-macro']
                break;

            case 'reporter-trade':
            case 'trade':
                url = '/trade/country/'+urlData['reporter-trade']+'/indicator-'+urlData['trade'].replace(/-/g, '_')
                break;

            case 'commodity':
            case 'currency':
            case 'equity':
                url = '/'+$av.data('active')+'/code/'+urlData[$av.data('active')]
                break;
        }

        window.location = siteData.data('url')+url;
    });
});

function toggleSearchNav(){
    // if($('.search-list-nav-btn[data-list-target="country"]').hasClass('active')) {
    //     $('.search-list-nav-country').slideDown()
    // } else {
    //     $('.search-list-nav-country').slideUp()
    //     $('.search-list-nav-country .btn').removeClass('active');
    // }
    //
    // if($('.search-list-nav-btn[data-list-target="report"]').hasClass('active')) {
    //     $('.search-list-nav-report').slideDown()
    // } else {
    //     $('.search-list-nav-report').slideUp()
    //     $('.search-list-nav-report .btn').removeClass('active');
    // }
    //
    // if($('.search-list-nav-btn[data-list-target="market"]').hasClass('active')) {
    //     $('.search-list-nav-market').slideDown()
    // } else {
    //     $('.search-list-nav-market').slideUp()
    //     $('.search-list-nav-market .btn').removeClass('active');
    // }

    $('.search-list-nav-market').slideDown()
}