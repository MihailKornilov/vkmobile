Highcharts.setOptions({
    lang: {
        months: ['������', '�������', '����', '������', '���', '����', '����', '������', '��������', '�������', '������', '�������']
    }
});

var G = {
    T:(new Date()).getTime(),
    vkScroll:0,
    zindex:100,
    backCount:0,
    backfon:function(add) {
        if(add === undefined)
            add = true;
        var body = $('body');
        if(add) {
            this.zindex += 10;
            if(this.backCount == 0) {
                body
                    .find('.backfon').remove().end()
                    .append('<div class="backfon"></div>');
            }
            body.find('.backfon').css({'z-index':this.zindex});
            this.backCount++;
        } else {
            this.backCount--;
            this.zindex -= 10;
            if(this.backCount == 0)
                body.find('.backfon').remove();
            else
                body.find('.backfon').css({'z-index':this.zindex});
        }
    }
};
/*
 * ������������ ���������
 * ������: 1 ����
 * G.end(count, ['���', '��', '��']);
*/
G.end = function (count, arr) {
  if (arr.length == 2) { arr.push(arr[1]); } // ���� � ������� ����� 2 ��������, �� ���������� ���, ������� ������ ������� � ������
  var send = arr[2];
  if(Math.floor(count / 10 % 10) != 1) {
    switch(count % 10) {
    case 1: send = arr[0]; break;
    case 2: send = arr[1]; break;
    case 3: send = arr[1]; break;
    case 4: send = arr[1]; break;
    }
  }
  return send;
}


// ������� �������� �������� ��������
$.fn.imgUp = function(ins) {
  $(this).find('.imgUp').remove();
  var img = "<IMG src=/img/upload.gif class=imgUp>";
  if (ins == 'append') {
    $(this).append(img);
  } else {
    $(this).html(img);
  }
}










// ������������� ������ 2013-02-14 23:19
$.fn.vkSel = function (obj) {
  var t = $(this);
  var id = t.attr('id');

  $("#vkSel_" + id).remove();    // �������� select ���� ����������

  $(document).off('click.results_hide').on('click.results_hide', function () {
    $(".vkSel")
      .find(".results").html('').end()
      .find(".ugol").css({'border-left':'#FFF solid 1px', 'background-color':'#FFF'});
    $(this)
      .off('keyup.vksel_esc')
      .off('keydown.vksel');
  });



  var obj = $.extend({
    width:150,            // ������
    bottom:0,             // ������ �����
    display:'block',     // ������������ �������
    title0:'',                 // ���� � ������� ���������
    spisok:[],             // ���������� � ������� json
    spisok_new:null, // ����������� ������ ������, ���� ������������ ����� � ��������
    limit:0,                  // ����������� �� ����� ���������� �������. ���� 0 - ��� �����������
    value:$(this).val() || 0, // ������� ��������
    ro:1,                     // ������ ����� � ���� INPUT
    nofind:'�� �������',  // ���������, ��������� ��� ������ ������
    func:null,              // �������, ����������� ��� ������ ��������
    funcAdd:null,        // ������� ���������� ������ ��������. ���� �� ������, �� ��������� ������. ������� ������� ������ ���� ���������, ����� ����� ���� �������� �����
    funcKeyup:null     // �������, ����������� ��� ����� � INPUT � �������. ����� ��� ������ ������ �� ���, ��������, Ajax-�������, ���� �� vk api. ��� ���� ro ������ ���� = 0.
  }, obj);




  // ������������� ������ ����������� ������
  var ass; ass_create();




  var html = "<DIV class=vkSel id=vkSel_" + id + " style=width:" + obj.width + "px;display:" + obj.display + ";>";

    html += "<TABLE class=main style=width:" + obj.width + "px;>";
      var sel_width = obj.width - 17 - 4;
      if (obj.funcAdd) { sel_width -= 17; }
      html += "<TD class=selected style=width:" + sel_width + "px; val=inp_>";
      html += "<INPUT type=text class=inp style=width:" + (sel_width - 5) + "px;" + (obj.ro ? "cursor:default; readonly" : '') + " val=inp_>";
      if (obj.funcAdd) { html += "<TD class=add val=add_>"; }
      html += "<TD class=ugol val=ugol_>";
    html += "</TABLE>";
    html += "<DIV class=results style=width:" + obj.width + "px;></DIV>";
  html += "</DIV>";

  $(this).after(html);



  var vksel = $("#vkSel_" + id);       // ���������� �������� �������
  var results = vksel.find(".results"); // ���������� ������ �� ���������
  var inp = vksel.find('.inp');              // ���������� ������ �� ���� ��� �����
  var keyup = 0;                               // ������������ ������� ������� (�����, ����� �� ����������� ������ ��� ��� ������ ��� �������) ��� keyupFunc
  var keyup_val;                              // �������� ����������� �����. ���� ����������, �� ������ �����������.

  // ������ �����, ���� ����������
  if (obj.bottom > 0) { vksel.css('margin-bottom', obj.bottom + 'px'); }

  // ��������� �������� � INPUT
  var inp_set = function (val) {
    if (val !== undefined) { obj.value = val }
    if (obj.title0 && obj.value == 0) {
      inp.val(obj.title0).css('color', '#888');
    } else {
      inp.val(ass[obj.value]).css('color', '#000');
    }
    t.val(obj.value);
    return this;
  };

  // ��������� �������� � INPUT
  inp_set();

  // ���� �������� ���� � INPUT, ����������� ����� �� ������
  if (!obj.ro) {
    inp
      .on('keyup', function (e) {
        if(e.keyCode != 38 && e.keyCode != 40 && e.keyCode != 13) {
          if (obj.funcKeyup) {
            var val = inp.val();
            if (keyup == 0 && keyup_val != val) {
              keyup_val = val;
              vksel.find(".process_inp").remove();
              inp.before("<DIV class=process_inp style=width:" + (sel_width - 5) + "px;><IMG src=/img/upload.gif></DIV>");
              keyup = 1; // ������� ���� ������. ������ ���������� �����.
              obj.funcKeyup(val);
            }
          } else { inp_write(); }
        }
      })
      .on('blur', function () { inp_set(); });
  }


  // ����������� � ����� ��������
  vksel.on({
    mouseenter:function () { $(this).find('.ugol:first').css({'border-left':'#d2dbe0 solid 1px', 'background-color':'#e1e8ed'}); }, // ��������� �������������
    mouseleave:function () { if (results.find('DL').length == 0) { $(this).find('.ugol:first').css({'border-left':'#FFF solid 1px', 'background-color':'#FFF'}); } },
    click:function (e) {
      var val = $(e.target).attr('val');
      if (val) {
        var arr = val.split('_');
        switch (arr[0]) {
        case 'ugol': // ���� �� ������
          $(document).off('keyup.vksel_esc').off('keydown.vksel'); // ���������� �������� ���� ������ � ����� ������
          vksels_hide(e);
          if (!results.find('DL').length) {
            if (obj.spisok_new != null && obj.spisok_new.length == 0) { obj.spisok_new = null; } // ���� ����� �� �������� ������ ������ ������ � ���������� ���� �������, �� ������������ ���� ������
            dd_create();
          } else { results.html(''); } // ���� ������ ��� ������, �� ��������
          break;

        case 'add': // ���� �� �������.
          obj.spisok_new = null; // ������� ������, ���� ������������ ����� �� ������
          obj.funcAdd(obj.spisok, t.o);
          break;

        case 'inp': // ���� �� ������
          vksels_hide(e);
          if (obj.ro != 1 && obj.title0 && obj.value == 0) { inp.val('').css('color', '#000'); }
          if (results.find('DL:first').length == 0) {
            if (obj.spisok_new != null && obj.spisok_new.length == 0) { obj.spisok_new = null; } // ���� ����� �� �������� ������ ������ ������ � ���������� ���� �������, �� ������������ ���� ������
            dd_create();
          } else if (obj.ro != 1) { inp_write(); }
          break;

        case 'title0':
          inp_set(0);
          if (obj.func) { obj.func(obj.value); }
          break;

        case 'dd':
          inp_set(arr[1]);
          if (obj.func) { obj.func(obj.value); }
          break;
        }
      }
    }
  });






  // �������� ������ � ����� � ���������
  function dd_create() {
    var spisok = obj.spisok_new != null ? obj.spisok_new : obj.spisok;
    var dd = "<DL>";
    var len = (obj.limit > 0 && spisok.length > obj.limit) ? obj.limit : spisok.length;
    if (obj.title0 && obj.ro == 1) { dd += "<DD class='" + (obj.value == 0 ? 'over' : 'out') + " title0' val=title0_0>" + obj.title0; }
    if (len > 0) {
      var reg = new RegExp(">", "ig");
      for (var n = 0; n < len; n++) {
        var sp = spisok[n];
        var c = sp.uid == obj.value ? 'over' : 'out'; // ��������� ���������� ��������
        var cont = null; // ������� val � �������������� ���� ��������
        if (sp.content) { cont = sp.content.replace(reg," val=dd_" + sp.uid + ">"); }
        dd += "<DD class=" + c + " val=dd_" + sp.uid + ">" + (cont ? cont : sp.title);
      }
    } else if (obj.ro != 1) { dd += "<DT class=nofind>" + obj.nofind; }
    dd += "</DL>";
    results.html(dd);

    dd = results.find("DD");
    len = dd.length;
    if (len > 0) {
      // ���������� ������ ����������� ������
      var dl = results.find("DL");
      var over;
      var results_h = results.css('height').split(/px/)[0]; // ������ ������ ����������� �� ������� ������ ���������
      if (results_h > 250) {
        dl.css({height:250 + 'px', 'border-bottom':'#CCC solid 1px'});
        // ����������� ���������� ���� � ���� ���������
        over = results.find('.over:first')[0];
        if (over) {
          var top = over.offsetTop + over.offsetHeight;
          if(top > 170) {
            var dl_h = 250;
            if (results_h > top) { dl_h -= results_h - top > 120 ? 120 : results_h - top; }
            dl[0].scrollTop = top - dl_h;
          }
        }
      } else { results.find("DD:last").addClass('last'); }

      // ��������� ��������� ����� �������� ��� ��������� ����
      dd.on('mouseenter', function () {
        $(this).parent().find('.over:first').removeClass('over').addClass('out');
        $(this).addClass('over');
      });

      // ���� ���������� �������, �� ��������� ESC ��� ������� �����������
      $(document).on('keyup.vksel_esc', function (ev) {
        if (ev.keyCode == 27) {
          $(document).off('keyup.vksel_esc').off('keydown.vksel');
          results.html('');
        }
      });

      dl = dl[0];
      $(document).on('keydown.vksel',function (e) {
        for (var n = 0; n < len; n++) { if(dd.eq(n).hasClass('over')) break; }
        switch (e.keyCode) {
        case 38: // ����������� �����
          e.preventDefault();
          if (n == len) { n = 1; }
          if (n > 0) {
            if (len > 1) { dd.eq(n).removeClass('over').addClass('out'); } // ���� � ������ ������ ����� ��������
            over = dd.eq(n-1);
          } else { over = dd.eq(0); }
          over.removeClass('out').addClass('over');
          over = over[0];
          if (dl.scrollTop > over.offsetTop) { dl.scrollTop = over.offsetTop; } // ���� ������� ���� ����� ���� ���������, �������� � ����� ����
          if (over.offsetTop - 250 - dl.scrollTop + over.offsetHeight > 0) { dl.scrollTop = over.offsetTop - 250 + over.offsetHeight; } // ���� ����, �� ����
          break;

        case 40: // ����������� ����
          e.preventDefault();
          if (n == len) { dd.eq(0).removeClass('out').addClass('over'); dl.scrollTop = 0; }
          if (n < len - 1) {
            dd.eq(n).removeClass('over').addClass('out');
            over = dd.eq(n+1);
            over.removeClass('out').addClass('over');
            over = over[0];
            if (over.offsetTop + over.offsetHeight - dl.scrollTop > 250) { dl.scrollTop = over.offsetTop + over.offsetHeight - 250; } // ���� ������� ���� ���������, �������� � ������ �������
            if (over.offsetTop < dl.scrollTop) { dl.scrollTop = over.offsetTop; } // ���� ����, �� � �������
          }
          break;

        case 13: // �����
          e.preventDefault();
          if (n < len) {
            inp_set(dd.eq(n).attr('val').split('_')[1]);
            results.html('');
            if (obj.func) { obj.func(obj.value); }
          }
          break;
        }
      }); // end keydown.vksel
    } // end len > 0
  }










  // �������� �������������� �������
  function ass_create() {
    var arr = [];
    for (var n = 0; n < obj.spisok.length; n++) {
      var sp = obj.spisok[n];
      arr[sp.uid] = sp.title;
    }
    ass = arr;
  }







  // ������� ����������� ���� �������� ����� ��������
  function vksels_hide(e) {
    e.stopPropagation();
    var s = $(".vkSel");
    for (var n = 0; n < s.length; n++) {
      var sp = s.eq(n);
      if (sp.attr('id').split('vkSel_')[1] != id) {
        sp
          .find('.results').html('').end()
          .find(".ugol").css({'border-left':'#FFF solid 1px', 'background-color':'#FFF'});
      }
    }
  }













  // �������� ������ �� ����������� ��������� ��� ����� � INPUT
  function inp_write() {
    obj.value = 0;
    var val = inp.val();
    if (val.length > 0) {
      obj.spisok_new = [];
      var tag = new RegExp("(<[\/]?[_a-zA-Z0-9=\"' ]*>)", 'i'); // ����� ���� �����
      var reg = new RegExp(val, 'i'); // ��� ������ ���������� ��������
      for (var n = 0; n < obj.spisok.length; n++) {
        var sp = obj.spisok[n];
        var replaced = 0; // ���������� � �������� �� ������������� ������
        var find = sp.content || sp.title; // ��� ����� ������������� �����
        var arr = find.split(tag); // �������� �� ������ �������� �����
        for (var k = 0; k < arr.length; k++) {
          var r = arr[k];
          if(r.length > 0) { // ���� ������ �� ������
            if (!tag.test(r)) { // ���� ��� �� ���
              if (reg.test(r)) { // ���� ���� ����������
                arr[k] = r.replace(reg, "<EM>$&</EM>"); // ������������ ������
                replaced = 1; // ������� � ������
                break; // � ����� ����� �� �������
              }
            }
          }
        }
        if (replaced == 1) { // ���� ������ ����, �� ����������� ����� ������
          obj.spisok_new.push({
            uid:sp.uid,
            title:sp.title,
            content:arr.join('')
          });
        }
        if (obj.limit > 0 && obj.spisok_new.length >= obj.limit) break;
      }
    } else { obj.spisok_new = null; }
    dd_create();
  }




  // �������� � ������ ������ ��������. (��� �������� �������������)
  var item_add = function (item) {
    obj.spisok.unshift(item);
    ass[item.uid] = item.title;
    return this;
  };

  // ������������ ������ ��� ���������� ����������� � ��������
  t.o = {
    spisok:function (spisok) { // ��������� ���� ��������� ������
      if (spisok != undefined) {
        obj.spisok = spisok;
        ass_create();
        vksel.find(".process:first").remove();
        if (obj.funcKeyup) { // ���������� ������, ���� �������� ������� ��� ����� � inp
          vksel.find(".process_inp:first").remove();
          if (keyup == 1) {
            inp_write();
            keyup = 0;
          } else { inp_set(0); }
        } else { inp_set(0); }
        return this;
      } else { return obj.spisok; }
    },

    val:function (val) { // ��������� ���� ��������� ��������
      if (val != undefined) {
        inp_set(val);
        return this;
      } else { return obj.value; }
    },

    add:item_add, // ���������� ������ ��������

    process:function () { // ��������� � ������ �������� �������� ��������� ������ ������. ��� ���� ������ ������ ���������. �������� �������� = 0.
      inp_set(0);
      inp.val('');
      obj.spisok = [];
      vksel.find(".process").remove();
      inp.before("<DIV class=process><IMG src=/img/upload.gif></DIV>");
    },

    remove:function () { vksel.remove(); return this; }
  };

  return t;
}; // end of vkSel



















// ���������
G.months_ass = {1:'������',2:'�������',3:'����',4:'������',5:'���',6:'����',7:'����',8:'������',9:'��������',10:'�������',11:'������',12:'�������'};
G.months_sel_ass = {1:'������',2:'�������',3:'�����',4:'������',5:'���',6:'����',7:'����',8:'�������',9:'��������',10:'�������',11:'������',12:'�������'};
(function () {
  var Calendar = function (obj, t) { this.create(obj, t); }

  Calendar.prototype.create = function (obj, t) {
    if (!obj) { var obj = {}; }

    this.id = t.attr('id');

    // ���� input hidden ������� ����, ��������� �
    var val = t.val();
    if (/^(\d{4})-(\d{1,2})-(\d{1,2})$/.test(val)) {
      var arr = val.split('-');
      obj.year = arr[0];
      obj.mon = Math.abs(arr[1]);
      obj.day = Math.abs(arr[2]);
    }

    var d = new Date();

    this.year = obj.year || d.getFullYear();     // ���� ��� �� ������, �� ������� ���
    this.mon = obj.mon || d.getMonth() + 1; // ���� ����� �� ������, �� ������� �����
    this.day = obj.day || d.getDate();           // �� �� � ����
    this.curYear = this.year;
    this.curMon = this.mon;
    this.curDay = this.day;

    this.lost = obj.lost || 0; // ���� �� 0, �� ����� ������� ��������� ���
    this.func = obj.func || function () {}; // ����������� ������� ��� ������ ���
    this.place = obj.place || 'right'; // ������������ ��������� ������������ ������

    var html = "<DIV class=vk_calendar>" +
      "<DIV class=cal_input val=cal_input>" + this.day + " " + G.months_sel_ass[this.mon] + " " + this.year + "</DIV>" +
      "<DIV class=cal_abs id=calabs_" + this.id + "></DIV>" +
    "</DIV>";

    t.next().remove('.vk_calendar'); // �������� ���������, ���� ��� ��� ����� ��������
    t.after(html);

    var cal = t.next();
    this.cShow = 0; // ������� ��������� ��� ���
    this.calAbs = cal.find('.cal_abs:first'); // ���������� ��� ���������
    this.calInput = cal.find('.cal_input:first'); // ��������� ����
    this.t = t;
    var thisCal = this;

    this.setVal();

    cal.on('click', function (e) {
      var val = $(e.target).attr('val');
      if (val) {
        var arr = val.split('cal_');
        switch (arr[1]) {
          case 'input': thisCal.calPrint(e); break;
          case 'back': thisCal.back(e); break;
          case 'next': thisCal.next(e); break;
          case 'lost': e.stopPropagation(); break; // ������� �� ��������� ����, ����� ������ ��������
          default: thisCal.setDay(arr[1]); break;
        }
      }
    });
  };


  // ��������� �������� input hidden
  Calendar.prototype.setVal = function () { this.t.val(this.dataForm()); };
  // ������������ ���� � ���� 2000-01-01
  Calendar.prototype.dataForm = function () { return this.curYear + "-" + (this.curMon < 10 ? '0' : '') + this.curMon + "-" + (this.curDay < 10 ? '0' : '') + this.curDay; };

  // �������� � ������� ���������
  Calendar.prototype.calPrint = function (e) {
    if (this.cShow == 0) {
      e.stopPropagation();
      // ���� ���� ������� ������ ���������, �� �����������, ����� ��������
      var calabs = $(".cal_abs");
      for (var n = 0; n <calabs.length; n++) {
        var sp = calabs.eq(n);
        if (sp.attr('id').split('calabs_')[1] == this.id) continue;
        sp.html('');
      }
      // �������� ��������� ��������� ��� ������� �� ����� ����� ������
      var thisCal = this;
      $(document).on('click.calendar' + this.id, function () {
        if (thisCal.cShow == 1) {
          thisCal.calAbs.html('');
          thisCal.cShow = 0;
          $(document).off('click.calendar' + thisCal.id);
        }
      });
      this.year = this.curYear;
      this.mon = this.curMon;
      var html = "<DIV class=cal_calendar style=left:" + (this.place == 'right' ? 0 : -64) + "px;>" +
          "<TABLE class=cal_head><TR><TD class=cal_back val=cal_back><TD class=cal_month><TD class=cal_next val=cal_next></TABLE>" +
          "<TABLE class=cal_week_name><TR><TD>��<TD>��<TD>��<TD>��<TD>��<TD>��<TD>��</TABLE>" +
          "<DIV class=cal_days></DIV>" +
        "</DIV>";
      this.calAbs.html(html);
      this.calMon = this.calAbs.find('.cal_month:first');
      this.calDays = this.calAbs.find('.cal_days:first');
      this.daysPrint();
      this.monPrint();
      this.cShow = 1;
    }
  };


  // ������������� ��������� �����
  Calendar.prototype.back = function (e) {
    e.stopPropagation();
    this.mon--;
    if (this.mon == 0) { this.mon = 12; this.year--; }
    this.daysPrint();
    this.monPrint();
  };


  // ������������� ��������� �����
  Calendar.prototype.next = function (e) {
    e.stopPropagation();
    this.mon++;
    if (this.mon == 13) { this.mon = 1; this.year++; }
    this.daysPrint();
    this.monPrint();
  };


  // ��������� ���
  Calendar.prototype.setDay = function (day) {
    this.curYear = this.year;
    this.curMon = this.mon;
    this.curDay = day;
    this.calInput.html(day + " " + G.months_sel_ass[this.mon] + " " + this.year);
    this.setVal();
    this.func(this.dataForm());
  };



  // ����� �������� ������ � ���� ������ ���������
  Calendar.prototype.monPrint = function () {  this.calMon.html(G.months_ass[this.mon] + " " + this.year); }



  // ����� ������ ����
  Calendar.prototype.daysPrint = function () {
    var html = "<TABLE><TR>";

    // ��������� ������ �����
    dayFirst = getDayFirst(this.year, this.mon);
    if (dayFirst > 1) { for (var n = 0; n < dayFirst - 1; n++) { html += "<TD>"; } }

    // ��������� �������� ���
    var d = new Date();
    var cur = 0;
    var year = d.getFullYear();
    var mon = d.getMonth() + 1;
    if (year == this.year && mon == this.mon) { cur = 1; }
    var today = d.getDate();

    // ��������� ���������� ���
    var st = 0;
    if (this.year == this.curYear && this.mon == this.curMon) { st = 1; }

    var dayCount = getDayCount(this.year, this.mon);
    for (var n = 1; n <= dayCount; n++) {
      var active = 'cal_day';
      if (this.year < year) { active = this.lost == 1 ? active + ' lost' : 'lost'; }
      else if (this.year == year && this.mon < mon) { active = this.lost == 1 ? active + ' lost' : 'lost'; }
      else if (this.year == year && this.mon == mon && n < today) { active = this.lost == 1 ? active + ' lost' : 'lost'; }
      var bold = (cur == 1 && n == today && active == 'cal_day' ? " cal-day-cur" : '');
      var back = (st == 1 && n == this.curDay ? " cal-day-set" : '');
      var val = " val=cal_" + (this.lost == 0 && active == 'lost' ? 'lost' : n); // ���� ������ ������� ��������� ����, �� ���� ����������� �� �����
      html += "<TD class='" + active + bold + back + "'" + val + ">" + n;
      dayFirst++;
      if (dayFirst == 8 && n != dayCount) {
        html += "<TR>";
        dayFirst = 1;
      }
    }
    html += "</TABLE>";
    this.calDays.html(html);
  }






  // ����� ������ ������ � ������
  function getDayFirst(year, mon) {
    var first = new Date(year, mon - 1, 1).getDay();
    if (first == 0) { return 7; } else { return first; }
  }



  // ���������� ���� � ������
  function getDayCount(year, mon) {
    mon--;
    if (mon == 0) { mon = 12; year--; }
    return 32 - new Date(year, mon, 32).getDate();
  }


  $.fn.vkCalendar = function (obj) { new Calendar(obj, $(this)); };
})();





























/* ���������� ���� �� ������
 *
 * id ����������� �� INPUT hidden
*/
$.fn.linkMenu = function (obj) {
  var obj = $.extend({
    head:'',    // ���� �������, �� �������� � �������� ������, � ������ �� spisok
    spisok:[],
    func:null,
    right:0    // ��������� ������ ��� ���
  },obj);

  var T = $(this);
  var idSel = T.val(); // ������� �������� � INPUT
  var selA = obj.head;  // ��������� ��� �� id
  var dl = '';
  var len = obj.spisok.length;
  for (var n = 0; n < len; n++) {
    dl += "<DD" + (n == len -1 ? ' class=last' : '') + " val=" + obj.spisok[n].uid + ">" + obj.spisok[n].title;
    if (idSel == obj.spisok[n].uid) {
      selA = obj.spisok[n].title;
    }
  }

  var attrId = "linkMenu_" + T.attr('id');
  var html = "<DIV class=linkMenu id=" + attrId + ">";
    html += "<A href='javascript:'>" + selA + "</A>";
    html += "<DIV class=fordl><DL><DT><EM>" + selA + "</EM>" + dl + "</DL></DIV>";
  html += "</DIV>";

  T.after(html);

  var ID = $("#" + attrId);
  var leftDl =  parseInt(ID.find('DL:first').css('left').split('px')[0]);

  ID.find("A:first").click(function () {
    var dd = getDD(T.val());
    if(dd) { dd.addClass('hover'); }
    $(this).next().show();
    if (obj.right) {
      var wDt = parseInt(ID.find("DT:first").css('text-align','right').css('width').split('px')[0]);
      var wEm = parseInt(ID.find('EM:first').css('width').split('px')[0]);
      ID.find('DL').css('left', (wEm - wDt + leftDl) + 'px');
    }
  });

  ID.find("DL").bind({
    mouseleave:function () {
      var forDL = $(this).parent();
      if(forDL.is(':visible')) {
        window.linkMenuDelay = window.setTimeout(function () { forDL.fadeOut(150); },500);
      }
    },
    mouseenter:function () {
      if (typeof window.linkMenuDelay == 'number') {
        window.clearTimeout(window.linkMenuDelay);
      }
    }
  });

  ID.find("DT").click(dlHide);

  ID.find("DD").bind({
    mouseenter:function () {
      ID.find(".hover").removeClass('hover');
      $(this).addClass('hover');
    },
    mouseleave:function () { $(this).removeClass('hover'); },
    click:function () {
      dlHide();
      var uid = $(this).attr('val');
      if (obj.func) { obj.func(uid); }
      // ���� head �� ������, �� ����� ������ ��� ��� ������
      if(!obj.head) {
        T.val(uid);
        var name = getDD(uid).html();
        ID.find("A:first").html(name);
        ID.find("DT:first").html(name);
      }
    }
  });

  function dlHide() { ID.find(".fordl").hide(); }

  function getDD (sel) {
    var dd = ID.find("DD");
    for (var n = 0; n < len; n++) {
      if (sel == obj.spisok[n].uid) {
        return dd.eq(n);
      }
    }
  return false;
  }
};






/* ���� - ������  */
$.fn.infoLink = function(obj) {
  var obj = $.extend({
    spisok:[],
    func:''
    },obj);
  var dl = '';
  for (var n = 0; n < obj.spisok.length; n++) {
      dl += "<DD val=" + obj.spisok[n].uid + ">" + obj.spisok[n].title;
  }
  var TS = $(this);
  TS.addClass('infoLink')
    .html("<INPUT type=hidden value='" + obj.spisok[0].uid + "'><DL>" + dl + "</DL>")
    .find('DD:first').addClass('sel');
  TS.find('DD').click(function () {
    TS.find('.sel').removeClass('sel');
    $(this).addClass('sel');
    if(obj.func) { obj.func($(this).attr('val')); }
  });
  return TS;
};
$.fn.infoLinkSet = function(id) {
  $(this).find('.sel').removeClass('sel');
  var dd = $(this).find('DD');
  for (var n = 0; n < dd.length; n++) {
    if(dd.eq(n).attr('val') == id) {
      dd.eq(n).addClass('sel');
      $(this).find("INPUT:first").val(id);
      break;
    }
  }
};






















// ���������� �������� ��������� ��� ����� ������
$.fn.butProcess = function () {
  var W=$(this).parent().css('width');
  $(this)
 // .attr('val',$(this).attr('onclick'))
//    .attr('name',$(this).html())
//    .attr('onclick',null)
    .html("&nbsp;<IMG src=/img/upload.gif>&nbsp;")
    .css({width:W});
}
// �������������� ����� ������
$.fn.butRestore = function () {
  var name = $(this).attr('name');
  var click = $(this).attr('val');
  $(this)
    .attr('onclick',click)
    .html(name);
}







/* ��������� ������ */
$.fn.topSearch = function (obj) {
  var obj = $.extend({
    width:126,
    focus:0,
    txt:'',
    func:'',
    enter:0    // ���� 1 - ������� ����������� ����� ������� Enter
    },obj);

  var TS = $(this);

  TS.addClass('topSearch').html("<H5><DIV>"+obj.txt+"</DIV></H5><H6><DIV class=img_del></DIV><INPUT TYPE=text></H6>");
  TS.find("H6:first").css('width', obj.width + 'px');
  var DIV = TS.find("H5 DIV:first");
  var input = TS.find("INPUT:first");
  input.css('width', (obj.width - 20) + 'px');

  DIV.click(function(){ input.focus(); });

  input.bind({
    focus:function () { DIV.css('color','#CCC'); },
    blur:function () { DIV.css('color','#777'); },
    keyup:function () {
      if (!$(this).val()) {
        DIV.show();
        $(this).prev().hide();
      } else {
        DIV.hide();
        $(this).prev().show();
      }
      if (obj.func && obj.enter == 0) { obj.func($(this).val()); }
    }
  });

  TS.find(".img_del").click(function () {
    $(this).hide().next().val('');
    DIV.show();
    if(obj.func) obj.func($(this).next().val());
  });

  if (obj.enter == 1) {
    input.keydown(function (e) {
      if(e.which==13 && obj.func)
        obj.func($(this).val());
      });
  }

  if(obj.focus == 1) { input.focus(); }

  return TS;
}

$.fn.topSearchClear = function () {
  this.find(".img_del").hide().next().val('');
  this.find("H5 DIV:first").show();
}

$.fn.topSearchSet = function (VAL) {
  if(!VAL) {
    this.find(".img_del").hide().next().val('');
    this.find("H5 DIV:first").show();
  } else {
    this.find(".img_del").show().next().val(VAL);
    this.find("H5 DIV:first").hide();
  }
    return $(this);
}












$.fn.vkCheck = function() {
    var t = $(this),
        id = t.attr('id'),
        val = t.val() == 1 ? 1 : 0;
    t.val(val);
    t.after('<div class="check' + val + '" id="' + id + '_check"></div>');
};

// �������
$.fn.myCheck = function (obj) {
  if (!obj) { obj = {}; }
  obj.uid = obj.uid || null;           // id ��������, ���� ��� INPUT ��� ������
  obj.title = obj.title || '';              // �������� ��������
  obj.value = obj.value || 0;       // ��������

  obj.top = obj.top ? " style='margin-top:" + obj.top + "px'" : ''; // ������ ������
  obj.bottom = obj.bottom ? " style='margin-bottom:" + obj.bottom + "px'" : ''; // ������ �����
  obj.br = obj.br ? "<BR>" : '';   // ������� �� ����� ������
  obj.func = obj.func || null;       // �������, ����������� ��� �������

  obj.spisok = obj.spisok || null; // ������ ��������� � ���� [{uid:1, title:'��������', value:0}]

  var t = $(this);
  var id = t.attr('id');

  // ������� ��� �������� INPUT
  if (t[0].tagName == 'INPUT') {
    if($("#check_" + id).length) { $("#check_" + id).remove(); } // ��������, ���� ����� �� ����������

    // ��������� � INPUT ��������, ���� ��� ������
    var val = t.val();
    if(val == '') {
      t.val(obj.value);
    } else {
      obj.value = val;
    }

    t.after("<DIV class=check" + obj.value + obj.top + obj.bottom + " id=check_" + id + ">" + obj.title + "</DIV>" + obj.br);

    // �������� ��� �������
    t.next().click(function () {
      var val = t.val() == 0 ? 1 : 0;
      t.val(val);
      $(this).attr('class', 'check' + val)
      if(obj.func) { obj.func(val); }
    });
  } else if (obj.spisok) { // ����� ������ ���������
    var html = '';
    for (var n = 0; n < obj.spisok.length; n++) {
      var sp = obj.spisok[n];
      var value = sp.value ? 1 : 0;
      html += "<INPUT type=hidden name=check_" + sp.uid + " id=check_" + sp.uid + " value=" + value + ">";
      html += "<DIV class=check" + value + obj.top + obj.bottom + " val=check_" + sp.uid + ">" + (sp.title ? sp.title : '') + "</DIV>" + obj.br;
    }
    t.html(html);
    t.unbind().bind('click',function (e) {
      var target = $(e.target);
      var val = target.attr('val');
      if (val) {
        var arr = val.split('_');
        if (arr[0] == 'check') {
          var input = $("#check_" + arr[1]);
          var o = {
            uid:arr[1],
            title:target.html(),
            value:input.val() == 0 ? 1 : 0,
            target:target
          };
          input.val(o.value);
          $(e.target).attr('class', 'check' + o.value);
          if(obj.func) { obj.func(o); }
        }
      }
    });
  } else {

  }

  return t;
};




$.fn.myCheckVal = function (val) {
  var id = $(this).attr('id');
  $("#check_" + id).attr('class','check' + (val ? val : 0));
};











// �����
$.fn.vkRadio = function (obj) {
  var t = $(this);
  var id = t.attr('id');
  var value = t.val(); // ������ ��������

  if (!obj) { var obj = {}; }
  obj.spisok = obj.spisok || [];
  obj.value = /\d$/.test(value) ? value : (obj.value || -1); // ������������� ��������. ����� ���������� ���� � INPUT, ���� ����� ������
  obj.display = "display:" + (obj.display || "block") +";";
  obj.top = obj.top ? "margin-top:" + obj.top + "px;" : '';
  obj.bottom = obj.bottom ? "margin-bottom:" + obj.bottom + "px;" : '';
  obj.right = obj.right ? "margin-right:" + obj.right + "px;" : '';
  obj.light = obj.light || 0; // ��������� ���������� ��������
  obj.func = obj.func || function () {};

    $('#' + id + '_radio').remove();
  var html = "<DIV class=radio id=" + id + "_radio val=end_>";
  for(var n = 0; n < obj.spisok.length; n++) {
    var sp = obj.spisok[n];
    html += "<DIV class=" + (sp.uid == obj.value ? 'on' : 'off') + " val=radio_" + obj.spisok[n].uid + " style=" + obj.display + obj.bottom + obj.top + obj.right + ">" + sp.title + "</DIV>";
  }
  html += "</DIV>";
  t.after(html);

  if (obj.light) { $("#" + id +"_radio .off").css('color', '#888'); }

  $("#" + id +"_radio").click(function (e) {
    var target = $(e.target);
    var n = 1;
    while (target.attr('val') == undefined) {
      target = target.parent();
      n--;
      if (n < 0) break;
    }
    var val = target.attr('val').split('_');
    if (val[0] == 'radio') {
      if (obj.value != val[1]) {
        obj.value = val[1];
        $(this).find(".on").attr('class', 'off').css('color', obj.light ? '#888' : '#000');
        target.attr('class', 'on').css('color', '#000');
        t.val(obj.value);
        obj.func(obj.value);
      }
    }
  });
};










// ������ �����
$.fn.myRadio = function(obj){
  var obj = $.extend({
    spisok:[{uid:0,title:'radio'}],
    bottom:0,
    func:''
  },obj);

  var INP = this;
  var ID = INP.attr('id');
  var VAL = INP.val();
  if (VAL.length == 0) VAL = -1;
  var HTML = "<DIV class=radio id="+ID+"_radio>";
  for(var n = 0; n < obj.spisok.length; n++) {
    HTML+="<DIV class=" + (obj.spisok[n].uid == VAL ? 'on' : 'off') + " val=" + obj.spisok[n].uid + ">" + obj.spisok[n].title + "</DIV>";
  }
  HTML += "</DIV>";
  INP.after(HTML);

  if(obj.bottom > 0) { $("#" + ID + "_radio DIV").css('margin-bottom',obj.bottom+'px'); }

  $("#"+ID+"_radio").click(function (e) {
    var target = $(e.target);
    while (target.attr('val') == undefined) { target = target.parent(); }
    var val = target.attr('val');
    $(this).find(".on").attr('class','off');
    target.attr('class','on');
    INP.val(val);
    if(obj.func) obj.func(val);
  });
};
$.fn.myRadioSet = function (VAL) {
  this.val(VAL);
  var ID=this.attr('id');
  var DIVS=$("#"+ID+"_radio DIV");
  DIVS.attr('class','off');
  var LEN=DIVS.length;
  for(var n=0;n<LEN;n++)
    if(VAL==DIVS.eq(n).attr('val'))
      DIVS.eq(n).attr('class','on');
};





















// ��������� vkHint 2013-02-14 14:43
(function () {
    var Hint = function (t, o) { this.create(t, o); return t; };

    Hint.prototype.create = function (t, o) {
        o = $.extend({
            msg:'��������� ���������',
            width:0,
            event:'mouseenter', // �������, ��� ������� ���������� �������� ���������
            ugol:'bottom',
            indent:'center',
            top:0,
            left:0,
            show:0,      // �������� �� ��������� ����� �������� ��������
            delayShow:0, // �������� ����� ���������
            delayHide:0, // �������� ����� ��������
            correct:0,   // ��������� top � left
            remove:0     // ������� ��������� ����� ������
        }, o);

        var correct = o.correct == 1 ? "<DIV class=correct>top: <SPAN id=correct_top>" + o.top + "</SPAN> left: <SPAN id=correct_left>" + o.left + "</SPAN></DIV>" : '';

        var html = "<TABLE class=cont_table>" +
            "<TR><TD class=ugttd colspan=3>" + (o.ugol == 'top' ? "<DIV class=ugt></DIV>" : '') +
            "<TR><TD class=ugltd>" + (o.ugol == 'left' ? "<DIV class=ugl></DIV>" : '') +
            "<TD class=cont>" + correct + o.msg +
            "<TD class=ugrtd>" + (o.ugol == 'right' ? "<DIV class=ugr></DIV>" : '') +
            "<TR><TD class=ugbtd colspan=3>" + (o.ugol == 'bottom' ? "<DIV class=ugb></DIV>" : '') +
            "</TABLE>";

        html = "<TABLE>" +
            "<TR><TD class=side012><TD>" + html + "<TD class=side012>" +
            "<TR><TD class=b012 colspan=3>" +
            "</TABLE>";

        html = "<TABLE class=hint_table>" +
            "<TR><TD class=side005><TD>" + html + "<TD class=side005>" +
            "<TR><TD class=b005 colspan=3>" +
            "</TABLE>";

        t.prev().remove('.hint'); // �������� ���������� ����� �� ���������
        t.before("<DIV class=hint>" + html + "</DIV>"); // ������� ����� ���������

        var hi = t.prev(); // ���� absolute ��� ���������
        var hintTable = hi.find('.hint_table:first'); // ���� ���������
        if (o.width > 0) { hintTable.find('.cont_table:first').width(o.width); }

        var hint_width = hintTable.width();
        var hint_height = hintTable.height();

        hintTable.hide().css('visibility','visible');

        // ��������� ����������� �������� � ������� ��� ������
        var top = o.top; // ��������� ��������� ���������
        var left = o.left;
        switch (o.ugol) {
            case 'top':
                top = o.top - 15;
                var ugttd = hintTable.find('.ugttd:first');
                if (o.indent == 'center') { ugttd.css('text-align', 'center'); }
                else if (o.indent == 'right') { ugttd.css('text-align', 'right'); }
                else if (o.indent == 'left') { ugttd.css('text-align', 'left'); }
                else if (!isNaN(o.indent)) {
                    ugttd.css('text-align', 'left');
                    if (o.indent < 10) { o.indent = 10; }
                    if (o.indent > hint_width) { o.indent = hint_width - 28; }
                    hintTable.find('.ugt:first').css('margin-left', o.indent + 'px');
                }
                break;

            case 'right':
                left = o.left + 25;
                var ugrtd = hintTable.find('.ugrtd:first');
                if (o.indent == 'center') { ugrtd.css('vertical-align', 'middle'); }
                else if (o.indent == 'bottom') { ugrtd.css('vertical-align', 'bottom'); }
                else if (!isNaN(o.indent)) {
                    if (o.indent < 3) { o.indent = 3; }
                    if (o.indent > hint_height) { o.indent = hint_height - 31; }
                    hintTable.find('.ugr:first').css('margin-top', o.indent + 'px');
                }
                break;

            case 'bottom':
                top = o.top + 15;
                var ugbtd = hintTable.find('.ugbtd:first');
                if (o.indent == 'center') { ugbtd.css('text-align', 'center'); }
                else if (o.indent == 'right') { ugbtd.css('text-align', 'right'); }
                else if (o.indent == 'left') { ugbtd.css('text-align', 'left'); }
                else if (!isNaN(o.indent)) {
                    ugbtd.css('text-align', 'left');
                    if (o.indent < 10) { o.indent = 10; }
                    if (o.indent > hint_width) { o.indent = hint_width - 28; }
                    hintTable.find('.ugb:first').css('margin-left', o.indent + 'px');
                }
                break;

            case 'left':
                left = o.left - 25;
                var ugltd = hintTable.find('.ugltd:first');
                if (o.indent == 'center') { ugltd.css('vertical-align', 'middle'); }
                else if (o.indent == 'bottom') { ugltd.css('vertical-align', 'bottom'); }
                else if (!isNaN(o.indent)) {
                    if (o.indent < 3) { o.indent = 3; }
                    if (o.indent > hint_height) { o.indent = hint_height - 31; }
                    hintTable.find('.ugl:first').css('margin-top', o.indent + 'px');
                }
                break;
        }




        // ���������� ������� �� ���������� ����� �� ���������
        t.off(o.event + '.hint');
        t.off('mouseleave.hint');

        // ��������� �������
        t.on(o.event + '.hint', show);
        t.on('mouseleave.hint', hide);
        hintTable.on('mouseenter.hint', show);
        hintTable.on('mouseleave.hint', hide);



        // �������� �������� ���������:
        // - wait_to_showind - ������� ������ (���� ���� ��������)
        // - showing - ���������
        // - show - ��������
        // - wait_to_hidding - ������� ������� (���� ���� ��������)
        // - hidding - ����������
        // - hidden - ������
        var process = 'hidden';

        var timer = 0;

        // �������������� ����� ���������, ���� �����
        if (o.show != 0) { show(); }

        // �������� ���������
        function show() {
            if (o.correct != 0) { $(document).off('keydown.hint'); }
            switch (process) {
                case 'wait_to_hidding': clearTimeout(timer); process = 'show'; break;
                case 'hidding':
                    process = 'showing';
                    hintTable
                        .stop()
                        .animate({top:top, left:left, opacity:1}, 200, showed);
                    break;
                case 'hidden':
                    if (o.delayShow > 0) {
                        process = 'wait_to_showing';
                        timer = setTimeout(action, o.delayShow);
                    } else { action(); }
                    break;
            }
            // �������� �������� ���������
            function action() {
                process = 'showing';
                hintTable
                    .css({top:o.top, left:o.left})
                    .animate({top:top, left:left, opacity:'show'}, 200, showed);
            }
            // �������� �� ���������� ��������
            function showed() {
                process = 'show';
                if (o.correct != 0) {
                    $(document).on('keydown.hint', function (e) {
                        e.preventDefault();
                        switch (e.keyCode) {
                            case 38: o.top--; top--; break; // �����
                            case 40: o.top++; top++; break; // ����
                            case 37: o.left--; left--; break; // �����
                            case 39: o.left++; left++; break; // ������
                        }
                        hintTable.css({top:top, left:left});
                        hintTable.find('#correct_top').html(o.top);
                        hintTable.find('#correct_left').html(o.left);
                    });
                }
            }
        } // end show




        // ������� ���������
        function hide() {
            if (o.correct != 0) { $(document).off('keydown.hint'); }
            if (process == 'wait_to_showing') { clearTimeout(timer); process = 'hidden'; }
            if (process == 'showing') { hintTable.stop(); action(); }
            if (process == 'show') {
                if (o.delayHide > 0) {
                    process = 'wait_to_hidding';
                    timer = setTimeout(action, o.delayHide);
                } else { action(); }
            }
            function action() {
                process = 'hidding';
                hintTable.animate({opacity:'hide'}, 200, function () {
                    process = 'hidden';
                    if (o.remove != 0) {
                        hi.remove();
                        t.off(o.event + '.hint');
                        t.off('mouseleave.hint');
                    }
                });
            }
        } // end hide
    };// end Hint.prototype.create

    $.fn.vkHint = function (obj) { return new Hint($(this), obj); };
})();







//��������� ������ ������ �������� ��� ������ ����
function frameBodyHeightSet(y) {
  var FB = document.getElementById('frameBody');
  if (!y) { FB.style.height = 'auto'; }
  var H = FB.offsetHeight-1;
  if(y && y > H) {
    H = y;
    FB.style.height = (H + 1) + 'px';
  }
  VK.callMethod('resizeWindow', 625, H);
}





//���������� ������
function setCookie(name,value) {
  var exdate = new Date();
  exdate.setDate(exdate.getDate() + 1);
  document.cookie=name + "=" + value + "; path=/; expires=" + exdate.toGMTString();
}
function delCookie(name) {
  var exdate = new Date();
  exdate.setDate(exdate.getDate()-1);
  document.cookie = name + "=; path=/; expires=" + exdate.toGMTString();
}
function getCookie(name) {
  var arr1 = document.cookie.split(name);
  if (arr1.length > 1) {
    var arr2 = arr1[1].split(/;/);
    var arr3 = arr2[0].split(/=/);
    return arr3[0] ? arr3[0] : arr3[1];
  } else {
    return null;
  }
}





//��������� � �������� ����������� ��������
function vkMsgOk(txt) {
    var obj = $("#vkMsgOk");
    if (obj.length > 0) { obj.remove(); }
    $("BODY").append("<DIV id=vkMsgOk>"+txt+"</DIV>");
    $("#vkMsgOk")
        .css('top',$(this).scrollTop() + 200 + G.vkScroll)
        .delay(1200)
        .fadeOut(400,function(){ $(this).remove(); });
}