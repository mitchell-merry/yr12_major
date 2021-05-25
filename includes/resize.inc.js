var lastWidth = document.body.clientWidth;
var wbp1 = 1000; // wbp = width breakpoint
var wbp2 = 800;

if(lastWidth < wbp1)
{
  togWidCls();
}

if(lastWidth < wbp2)
{
  togWidCls2();
}

var lastHeight = document.body.clientHeight;
var lastTotalHeight = 0;

onResize();

function onResize()
{
  var width = document.body.clientWidth;
  if((width < wbp2 && lastWidth >= wbp1) || (width >= wbp1 && lastWidth < wbp2))
  {
    togWidCls();
    togWidCls2();
  }
  else if((width < wbp1 && lastWidth >= wbp1) || (width >= wbp1 && lastWidth < wbp1))
  {
    togWidCls();
  }
  else if((width < wbp2 && lastWidth >= wbp2) || (width >= wbp2 && lastWidth < wbp2))
  {
    togWidCls2();
  }
  lastWidth = width;

  mstory();
  ad();
  cf();

  var center_w = document.getElementById("center-wrapper");

  if(width <= 558)
  {
    center_w.classList.add("pt-5");
  }
  else
  {
    center_w.classList.remove("pt-5");
  }
}

function cf()
{
  var cf = document.getElementById("cf");
  if(cf)
  {
    var cf_con = document.getElementById("cf-con");
    	/* margin-left: calc(calc(100% - var(--w)) / 2); */

    cf.style.marginLeft = (cf_con.getBoundingClientRect().width - cf.getBoundingClientRect().width)/2 + "px";
    // console.log(cf_con.getBoundingClientRect().width + " " + cf.getBoundingClientRect().width);
  }
}

function ad()
{
  onScroll();
  var left = document.getElementById("left");
  var ad = document.getElementById("ad");

  var lw = $('#left').outerWidth();
  var adw = $('#ad').outerWidth();

  // ad.style.left = "400px";
  var a = (lw - adw - left.getBoundingClientRect().left)/2;
  // console.log(a);
  ad.style.left = a + "px";
  // console.log("Left: " + );
}

function mstory()
{
  // Minor story shiz ma mans
  var navHeight = document.getElementById("nav").clientHeight;
  // console.log(navHeight)
  var height = document.body.clientHeight - navHeight;
  var wrapper = document.getElementById("mstory-cf");
  var stories = wrapper.children;

  var vsc = 0; // visibleStoryCount
  var totalHeight = 0;

  for (var i = 0; i < stories.length; i++) {
    h = stories[i].clientHeight;
    totalHeight += h;

    // TODO FIX THIS SHIT

    // if(totalHeight > height)
    // {
    //   stories[i].classList.add('hidden2');
    //
    // }
    // else
    // {
    //   try
    //   {
    //     if(totalHeight + h > height)
    //     {
    //       console.log("righr")
    //       stories[i-1].classList.add('bb');
    //
    //     }
    //     else
    //     {
    //       stories[i-1].classList.remove('bb');
    //     }
    //   }
    //   catch(e)
    //   {
    //     console.log(e.message + ", i:" + i + ", height:" + height + ", totalHeight:" + totalHeight + ", h:" + h);
    //   }
    //
    //   stories[i].classList.remove('hidden2');
    //
    // }
    // console.log(totalHeight + " " + height + " " + i);
   }
}

function togWidCls()
{
  var left = document.getElementById("left");
  var center = document.getElementById("center");
  var center_w = document.getElementById("center-wrapper");
  var right = document.getElementById("right");

  left.classList.toggle('col-2');
  left.classList.toggle('hidden');
  center.classList.toggle('col-7');
  center.classList.toggle('col-8');
  center.classList.toggle('mt-8');
  center_w.classList.toggle('pl-3');
  right.classList.toggle('col-3');
  right.classList.toggle('col-4');

}

function togWidCls2()
{
  var center = document.getElementById("center");
  var center_w = document.getElementById("center-wrapper");
  var right = document.getElementById("right");

  var ng_list = document.getElementById("navbar-genres");
  var login = document.getElementById("navbar-login");

  center.classList.toggle('col-8');
  center.classList.toggle('col-12');
  center_w.classList.toggle('pr-3');
  right.classList.toggle('col-4');
  right.classList.toggle('hidden');


  ng_list.classList.toggle('col-9');
  ng_list.classList.toggle('col-5');
  login.classList.toggle("col-3");
  login.classList.toggle("col-7");
}
