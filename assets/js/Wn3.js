// * <------------------------------------------------------------> 

var click= document.getElementById('menu-click');
var clicks= document.getElementById('menu-clicks');
    var cart = document.getElementById('cart')
    var cartwrapper = document.getElementById('cart-wrapper')
    var cartclose = document.getElementById('cart-close')
    var header = document.getElementById('header')
    click.onclick = function() {
        cart.style.display='block';
        cartwrapper.style.display='block';
        document.body.style.overflow='hidden';
    }
    cart.onclick = function() {
        cart.style.display='none';
        cartwrapper.style.display='none';
        document.body.style.overflow='visible';
    }
    cartclose.onclick = function() {
        cart.style.display='none';
        cartwrapper.style.display='none';
        document.body.style.overflow='visible';
    }
    clicks.onclick = function() {
        cart.style.display='block';
        cartwrapper.style.display='block';
        document.body.style.overflow='hidden';
    }

// * <------------------------------------------------------------> 

const buy = document.querySelectorAll(".buy")
    buy.forEach(function(a,index) {
    a.addEventListener("click",function(event){{
        var purchase = event.target
        var chivasbuy = purchase.parentElement.parentElement.parentElement.parentElement
        var chivasimg = chivasbuy.querySelector("img").src
        var chivasname = chivasbuy.querySelector(".chivas-name").innerText
        var chivasprice = chivasbuy.querySelector(".price").innerText
        var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
        product.push({
            name: chivasname,
            img: chivasimg,
            price: chivasprice
        })
        localStorage.setItem("product",JSON.stringify(product))
        addcart()
        checkname()
        joincart()
    }})
    }) 

function addcart() {
    var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
    var ulcontent = []
    product.map((innerHTML,index)=>{
        ulcontent += `<ul class="widget-product">
            <li class="widget-products">
                <a href=""><img src="${innerHTML.img}" alt="" class="widget-product-img"></a>
                <button class="btn-widget"><i  class="widget-product-icon fa-solid fa-xmark" onclick="deletecart(${index})"></i></button>
                <a href="" class="widget-product-text">${innerHTML.name}</a>
                <span class="amount">
                    <span class="amount-price" type ="number">${innerHTML.price}</span>
                    <span class="amount-price-d">$</span>
                    <i class="x-icon fa-solid fa-xmark"></i>
                    <input class="input-value" type="number" value="1" min="1" step="1" max="9999">
                </span>
            </li>
        </ul> `;
        
    }) 
    document.getElementById("widget").innerHTML = ulcontent
    var  cartitem = document.querySelectorAll(".widget-product")
    for(var i=0;i<cartitem.length;i++) {
        var footercart = document.querySelector(".footer-cart") 
        var carttext = document.querySelector(".cart-text")  
            if(cartitem[i]) { 
                footercart.style.display = "block"
                carttext.style.display = "none"
            }
    }
   carttotal()
}        

function checkname() {
    var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
    var checkname = document.querySelector(".widget-product-text").innerHTML
    var quantity = parseInt(document.querySelector(".input-value").value)
    
    product.find((name)=>{
        if(name == checkname) {
            return quantity+1;

        }
    })

}


function carttotal() {
    var  cartitem = document.querySelectorAll(".widget-product")
    var totalB = 0
    for(var i=0;i<cartitem.length;i++) {
        var inputValue = cartitem[i].querySelector(".input-value").value
        var productprice = cartitem[i].querySelector(".amount-price").innerHTML
         totalA = inputValue*productprice*1000
         totalB = totalB + totalA
    }
    var carttotalA = document.querySelector(".provisional-price") 
    totalC = totalB.toLocaleString('de-DE')
    carttotalA.innerHTML = totalC
    var carttotalD = document.querySelector(".cart-price")
    var carttotalE = document.querySelector(".cart-prices")
    carttotalD.innerHTML = totalC
    carttotalE.innerHTML = totalC
    if(totalC == 0) {
        var footercart = document.querySelector(".footer-cart") 
        var carttext = document.querySelector(".cart-text") 
            footercart.style.display = "none"
            carttext.style.display = "block"
    }
}

function deletecart(index) {
    var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
    product.splice(index,1)
    localStorage.setItem("product",JSON.stringify(product))
    addcart()
}

function joincart() {
    var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
    var yield = []
    product.map((innerHTML,index)=>{
        yield += `<tr class="cart-item" >
            <td class="product-remove"><i class="remove fa-solid fa-xmark" onclick="removechivas(${index})"></i></td>
            <td class="product-img">
                <img class="primg" src="${innerHTML.img}" style="width: 75px; height:100px;" alt="" >
            </td>
            <td class="product-name" data-title="Product">
                <a href="" class="chivas-name">${innerHTML.name}</a>
            </td>
            <td class="product-price" data-title="Price">
                <span class="price-text" type="number">${innerHTML.price}</span>
                <span class="price-d">$</span>
            </td>
            <td class="product-quantity" data-title="amount">
                <div class="quantity-add">
                    <input type="button" value="-" class="add is-form">
                    <input aria-label="quantity" type="number" class="number" step="1" min="1" max="9999" value="1" title="SL" inputmode="numeric">
                    <input type="button" value="+" class="apart is-form">
                </div> 
            </td>
            <td class="product-subtotal" data-title="provisinal">
                <span class="price-texts">${innerHTML.price}</span>
                <span class="price-d">$</span>
            </td>
        </tr>`
    }) 
    document.getElementById("contain-product").innerHTML = yield
    var  cartitems = document.querySelectorAll(".cart-item")
    for(var i=0;i<cartitems.length;i++) {
        var back = document.querySelector(".content-text") 
        var payment = document.querySelector("#content-product")  
            if(cartitems[i]) { 
                payment.style.display = "block"
                back.style.display = "none"
            } 
    }
    inputalter()
    provisional()
}

function inputalter() {
     $('input.number').each(function() {
          var $this = $(this),
            qty = $this.parent().find('.is-form'),
            min = Number($this.attr('min')),
            max = Number($this.attr('max'))
          if (min == 0) {
            var d = 0
          } else d = min
          $(qty).on('click', function() {
            if ($(this).hasClass('add')) {
              if (d > min) d += -1
            } else if ($(this).hasClass('apart')) {
              var x = Number($this.val()) + 1
              if (x <= max) d += 1
            }
            $this.attr('value', d).val(d)
            update()
          })
        }) 
        
}


function provisional() {
    var  chivasproduct = document.querySelectorAll(".cart-item")
    var totalpr = 0
    for(var i=0;i<chivasproduct.length;i++) {
        var numbervalue = chivasproduct[i].querySelector(".number").value
        var chivasmoney = chivasproduct[i].querySelector(".price-texts").innerHTML
        money = numbervalue*chivasmoney*1000
        totalpr = totalpr + money
    }
    totalprs = totalpr.toLocaleString('de-DE')
    var scrollprice = document.querySelector(".cart-price")
        scrollprice.innerHTML = totalprs
    var matheadprice = document.querySelector(".cart-prices")
        matheadprice.innerHTML=totalprs
    var chivaspro = document.querySelector(".provisional")
        chivaspro.innerHTML = totalprs
    var chivaspros = document.querySelector(".provisionals")
        chivaspros.innerHTML = totalprs   
    var back = document.querySelector(".content-text") 
    var payment = document.querySelector("#content-product")  
    if(totalprs == 0) {
        payment.style.display = "none"
        back.style.display = "block"
    }
}

function removechivas(index) {
    var product = localStorage.getItem("product") ? JSON.parse(localStorage.getItem("product")) : [];
    product.splice(index,1)
    localStorage.setItem("product",JSON.stringify(product))
    joincart()   
}

function update() {
    var update = document.querySelector(".update-cart")
        update.style.opacity = "1";
        update.addEventListener("click",function(){
            provisional()
            update.style.opacity = "0.6";
        })
}



// * <------------------------------------------------------------> 
const addproduct = document.querySelector(".btn-add")
    addproduct.onclick = function() {
        var name = document.querySelector(".name").value
        var price = document.querySelector(".price").value
        var img = document.querySelector("#img-preview").src
        moreproduct(name,price,img)
    }

function moreproduct(name,price,img) {
    var addli= document.createElement("li");
    var addinfo = '<div class="infomation"><span>'+name+'</span></div><div class="infomation" style="justify-content: center;"><img src="'+img+'" alt=""></div><div class="infomation"><span>'+price+'&nbsp;$</span></div><div class="btnfun"><button class="edit" style="margin-left: 15px;">Edit</button><button class="remove" style=" width: 75px;margin-left:7px;">Remove</button></div>'
    addli.innerHTML = addinfo
    var ul = document.querySelector(".info-nav")
    ul.append(addli)
    clearproduct()
}

function clearproduct() {
    var abcxyz  = document.querySelectorAll(".info-nav li")
    for(var i=0;i<abcxyz.length;i++) {
        var remove = document.querySelectorAll(".remove") 
        remove[i].addEventListener("click",function(event){
            var remove1 = event.target
            var remove2 = remove1.parentElement.parentElement 
            remove2.remove()
        })
    }
}

const removepro = document.querySelectorAll(".remove")
    removepro.forEach(function(b,index) {
        b.addEventListener("click",function(event){{
        var remove1 = event.target
        var remove2 = remove1.parentElement.parentElement
        remove2.remove()
        
    }})
}) 


// * <------------------------------------------------------------> 

var slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
  showSlides(slideIndex += n);
}

function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("banner-fill");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  slides[slideIndex-1].style.display = "block";
    

  var slides1 = document.getElementsByClassName("banner");
  if (n > slides1.length) {slideIndex = 1}
  if (n < 1) {slides1Index = slides1.length}
  for (i = 0; i < slides1.length; i++) {
      slides1[i].style.display = "none";
  }
  slides1[slideIndex-1].style.display = "block";
}


var myIndex = 0;
carousel2();

function carousel2() {
  var i;
  var x = document.getElementsByClassName("banner-fill");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  myIndex++;
  if (myIndex > x.length) {myIndex = 1}    
  x[myIndex-1].style.display = "block";  
  setTimeout(carousel2, 20000);
}

var myIndex1 = 0;
carousel3();

function carousel3() {
  var i;
  var x = document.getElementsByClassName("banner");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  myIndex1++;
  if (myIndex1 > x.length) {myIndex1 = 1}    
  x[myIndex1-1].style.display = "block";  
  setTimeout(carousel3, 20000);    
}


// * <------------------------------------------------------------> 


function clickdesc() {
    desc = document.querySelector(".describe").style.display = "block";
    rev = document.querySelector(".review").style.display = "none";  
    textdr = document.querySelector(".text-dr").style.color = "rgb(0 0 0/90%)";
    textdrs = document.querySelector(".text-drs").style.color = "rgb(0 0 0/50%)";
    line = document.querySelector(".line").style.opacity = "1";
    lines = document.querySelector(".lines").style.opacity = "0";
}


function clickrev() {
    desc = document.querySelector(".describe").style.display = "none";
    rev = document.querySelector(".review").style.display = "block";  
    textdr = document.querySelector(".text-dr").style.color = "rgb(0 0 0/50%)";
    textdrs = document.querySelector(".text-drs").style.color = "rgb(0 0 0/90%)";
    lines = document.querySelector(".lines").style.opacity = "1";
    line = document.querySelector(".line").style.opacity = "0";
}


//  * <------------------------------------------------->

function clickaddproduct() {
    info = document.querySelector(".management-info").style.display = "none"
    addpro = document.querySelector(".management-addpro").style.display = "block"
}

function clickinfo() {
    info = document.querySelector(".management-info").style.display = "block"
    addpro = document.querySelector(".management-addpro").style.display = "none"
}

//  * <------------------------------------------------->

