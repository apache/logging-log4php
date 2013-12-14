$(function() {
    reformatTabs();
    activateTabs();
});

function reformatTabs()
{
    $('.container.tabs').each(function(tabs_id, container) {
        var i, count, title, tabID;
        var $menu, $tabs, $tab, $titles;

        $tabs = $(container).children('div');
        $titles = $(container).children('p.rubric');

        if ($tabs.size() < 1) {
            console.warn("Tab init failed. Not enough <div>s. Required at least 1.");
        }

        if ($tabs.size() !== $titles.size()) {
            console.warn("Tab init failed. Number of <div>s does not match number of <p.rubric>s.");
            return;
        }

        count = $tabs.size();
        $menu = $('<ul>');

        for (i = 0; i < count; i++) {
            $tab = $($tabs[i]);
            title = $titles[i].innerHTML;
            tabID = 'tab-' + tabs_id + '-' + i;

            $($titles[i]).remove();
            $menu.append('<li><a href="#' + tabID + '">' +  title + '</a></li>');
            $tab.attr('id', tabID);
        }

        $(container).prepend($menu);
    })
}

function activateTabs()
{
    // Hide all tabs
    $('.container.tabs > div').hide();

    // For each tabs container show first tab and activate first label
    $('.container.tabs').each(function() {
        $(this).children('div:first').show();
        $(this).find('li:first').addClass('active');
    });

    // On tab click
    $('.container.tabs ul li a').click(function(event) {

        event.preventDefault();

        var list_item = $(this).parent();
        var list = list_item.parent();
        var container = list.parent();

        list.children().removeClass('active');
        list_item.addClass('active');

        container.children('div').hide();
        var currentTab = $(this).attr('href');
        $(currentTab).show();
    });
}
