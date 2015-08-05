$(function () {
    $('.side-menu').metisMenu();
});

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function () {
    $(window).bind("load resize", function () {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1)
            height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;


    var current = $('ul.side-menu a[href]').filter(function () {
        return this.href == url || this.href == url + '/';
    });

    current.addClass('active');

    //Find most suitable links
    if (current.length === 0) {
        var links = $('ul.side-menu a[href]').filter(function () {
            return url.href.indexOf(this.href) === 0;
        });

        var minUrlDiff = 999999;
        var suitableLinks = [];

        links.each(function () {
            var diff = String(url).length - $(this).attr('href').length;
            if (minUrlDiff > diff) {
                minUrlDiff = diff;
                suitableLinks = [];
            }
            if (minUrlDiff === diff) {
                suitableLinks.push($(this));
            }
        });

        $(suitableLinks).each(function () {
            $(this).addClass('active');
        });
    }

    var element = $('ul.side-menu a[href]').filter(function () {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).parent().parent().addClass('in').parent();

    if (element.is('li')) {
        element.addClass('active');
    }
});


$(function () {
    setTimeout(function () {
        // $('.glyphicon-select').remove();
    }, 2000);

});