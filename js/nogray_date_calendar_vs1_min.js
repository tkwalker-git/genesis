//http://www.nogray.com/license.php
var _0 = {
    language: {
        'days': {
            'char': ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
            'short': ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            'mid': ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            'long': ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
        },
        'months': {
            'short': ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'long': ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
        },
        'am_pm': {
            'lowerCase': ['am', 'pm'],
            'upperCase': ['AM', 'PM']
        }
    },
    daysInMonth: function () {
        var a = new Date(this.getFullYear(), this.getMonth(), 28);
        var b = 28;
        for (b = 28; b <= 32; b++) {
            a.setDate(b);
            if (a.getMonth() != this.getMonth()) return (b - 1)
        }
    },
    isLeapYear: function () {
        var a = new Date(this.getFullYear(), 1, 29);
        return (a.getMonth() == 1)
    },
    fromString: function (a) {
        var b = Date.parse(a.replace(/[-|\\]/g, "/"));
        if (isNaN(b)) {
            a = a.toLowerCase();
            a = a.replace(/(\s)*([\+|-])(\s)*/g, "$2");
            var c = this.getFullYear();
            var d = this.getMonth();
            var f = this.getDate();
            a = a.replace("yesterday", "today-1").replace("tomorrow", "today+1").replace("last month", "month-1").replace("next month", "month+1").replace("last year", "year-1").replace("next year", "year+1");
            if (a.indexOf("today+") >= 0) f = f + a.replace("today+", "").toInt();
            else if (a.indexOf("today-") >= 0) f = f - a.replace("today-", "").toInt();
            else if (a.indexOf("month+") >= 0) {
                d = d + a.replace("month+", "").toInt();
                var g = new Date(c, d, 1).daysInMonth();
                if (f > g) f = g
            } else if (a.indexOf("month-") >= 0) {
                d = this.getMonth() - a.replace("month-", "").toInt();
                var g = new Date(c, d, 1).daysInMonth();
                if (f > g) f = g
            } else if (a.indexOf("year+") >= 0) {
                c = c + a.replace("year+", "").toInt();
                var g = new Date(c, d, 1).daysInMonth();
                if (f > g) f = g
            } else if (a.indexOf("year-") >= 0) {
                c = this.getFullYear() - a.replace("year-", "").toInt();
                var g = new Date(c, d, 1).daysInMonth();
                if (f > g) f = g
            }
            var j = new Date(c, d, f, this.getHours(), this.getMinutes(), this.getSeconds(), this.getMilliseconds())
        } else {
            var j = new Date(b)
        }
        return j
    },
    fromObject: function (a) {
        var b = {};
        var c;
        for (c in a) b[c] = a[c];
        if (!$defined(b.date)) b.date = this.getDate();
        if (!$defined(b.month)) b.month = this.getMonth();
        if (!$defined(b.year)) b.year = this.getFullYear();
        if (!$defined(b.hour)) b.hour = this.getHours();
        if (!$defined(b.minute)) b.minute = this.getMinutes();
        if (!$defined(b.second)) b.second = this.getSeconds();
        if (!$defined(b.millisecond)) b.millisecond = this.getMilliseconds();
        if ($type(b.date) != "string") {
            var d = new Date(b.year, b.month, b.date, b.hour, b.minute, b.second, b.millisecond);
            return d
        }
        b.date = b.date.toLowerCase();
        var f = new Date(b.year, b.month, 1);
        var g;
        if (b.date.indexOf("sunday") != -1) g = 0;
        else if (b.date.indexOf("monday") != -1) g = 1;
        else if (b.date.indexOf("tuesday") != -1) g = 2;
        else if (b.date.indexOf("wednesday") != -1) g = 3;
        else if (b.date.indexOf("thursday") != -1) g = 4;
        else if (b.date.indexOf("friday") != -1) g = 5;
        else if (b.date.indexOf("saturday") != -1) g = 6;
        if (f.getDay() > g) var j = (7 - f.getDay()) + g + 1;
        else if (f.getDay() < g) var j = g - f.getDay() + 1;
        else var j = 1;
        var l = ["1st", "2nd", "3rd", "4th", "5th"];
        var n = 5;
        var m = f.daysInMonth();
        while (b.date.indexOf("last") != -1) {
            if ((j + (n * 7)) <= m) b.date = b.date.replace("last", l[n]);
            n--;
            if (n < 0) b.date = b.date.replace("last", "1st")
        }
        var k;
        if (b.date.indexOf("1st") != -1) k = 0;
        else if (b.date.indexOf("2nd") != -1) k = 1;
        else if (b.date.indexOf("3rd") != -1) k = 2;
        else if (b.date.indexOf("4th") != -1) k = 3;
        else if (b.date.indexOf("5th") != -1) k = 4;
        var d = new Date(b.year, b.month, j + (k * 7), b.hour, b.minute, b.second, b.millisecond);
        return d
    },
    print: function (a, b) {
        if (!$defined(b)) b = this.language;
        else {
            if ($defined(b.days)) {
                if (!$defined(b.days['char'])) b.day['char'] = this.language.days['char'];
                if (!$defined(b.days['short'])) b.day['short'] = this.language.days['short'];
                if (!$defined(b.days['mid'])) b.day['mid'] = this.language.days['mid'];
                if (!$defined(b.days['long'])) b.day['long'] = this.language.days['long']
            } else b.days = this.language.days;
            if ($defined(b.months)) {
                if (!$defined(b.months['short'])) b.months['short'] = this.language.months['short'];
                if (!$defined(b.months['long'])) b.months['long'] = this.language.months['long']
            } else b.months = this.language.months;
            if ($defined(b.am_pm)) {
                if (!$defined(b.am_pm['lowerCase'])) b.am_pm['lowerCase'] = this.language.am_pm['lowerCase'];
                if (!$defined(b.am_pm['upperCase'])) b.am_pm['upperCase'] = this.language.am_pm['upperCase']
            } else b.am_pm = this.language.am_pm
        }
        var c = 0;
        var d = "";
        var f = "";
        for (c = 0; c < a.length; c++) {
            f = a.charAt(c);
            if (f == "d") {
                if (this.getDate() < 10) d += "0";
                d += this.getDate()
            } else if (f == "D") d += b.days['mid'][this.getDay()];
            else if (f == "j") d += day;
            else if (f == "l") d += b.days['long'][this.getDay()];
            else if (f == "N") {
                var g = this.getDay();
                if (g == 0) g = 7;
                d += g
            } else if (f == "S") {
                if ((this.getDate() == "1") || (this.getDate() == "21") || (this.getDate() == "31")) d += "st";
                else if ((this.getDate() == "2") || (this.getDate() == "22")) d += "nd";
                else if ((this.getDate() == "3") || (this.getDate() == "23")) d += "rd";
                else d += "th"
            } else if (f == "w") d += this.getDay();
            else if (f == "z") d += this.getDayInYear();
            else if (f == "F") d += b.months['long'][this.getMonth()];
            else if (f == "M") d += b.months['short'][this.getMonth()];
            else if (f == "m") {
                if (this.getMonth() + 1 < 10) d += 0;
                d += this.getMonth() + 1
            } else if (f == "n") d += this.getMonth();
            else if (f == "t") d += this.daysInMonth();
            else if (f == "L") {
                if (this.isLeapYear()) d += 1;
                else d += 0
            } else if ((f == "Y") || (f == "o")) d += this.getFullYear();
            else if (f == "y") d += this.getFullYear().toString().substr(2, 2);
            else if (f == "a") {
                if (this.getHours() < 12) d += b.am_pm['lowerCase'][0];
                else d += b.am_pm['lowerCase'][1]
            } else if (f == "A") {
                if (this.getHours() < 12) d += b.am_pm['upperCase'][0];
                else d += b.am_pm['upperCase'][1]
            } else if (f == "B") d += this.toSwatchInternetTime();
            else if (f == "g") {
                var j = (this.getHours() % 12);
                if (j == 0) j = 12;
                d += j
            } else if (f == "G") d += this.getHours();
            else if (f == "h") {
                var j = (this.getHours() % 12);
                if (j == 0) j = 12;
                if (h < 10) r += 0;
                d += j
            } else if (f == "H") {
                if (this.getHours() < 10) d += 0;
                d += this.getHours()
            } else if (f == "i") {
                if (this.getMinutes() < 10) d += 0;
                d += this.getMinutes()
            } else if (f == "s") {
                if (this.getSeconds() < 10) d += 0;
                d += this.getSeconds()
            } else if (f == "u") d += this.getMilliseconds();
            else if ((f == "O") || (f == "P")) {
                var j = (this.getTimezoneOffset()) / 60;
                var l = j - Math.floor(j);
                l = l * 60;
                j = Math.floor(j);
                l = Math.floor(l);
                if (j == 0) j = "00";
                else if ((j > -10) && (j < 0)) j = "-0" + Math.abs(j);
                else if ((j < 10) && (j > 0)) j = "0" + j;
                else j = j.toString();
                if (j > 0) d += "+";
                if (l < 10) l = "0" + l;
                else l = l.toString();
                if (f == "P") var n = ":";
                else var n = "";
                d += j + n + l
            } else if (f == "Z") d += this.getTimezoneOffset();
            else if (f == "c") {
                a = a.substr(0, c - 0) + "Y-m-dTH:i:sP" + a.substr(0, c);
                c--
            } else if (f == "r") {
                a = a.substr(0, c - 0) + "D, d M Y H:i:s O" + a.substr(0, c);
                c--
            } else if (f == "U") d += Math.floor(this.timeDifference(new Date(1970, 0, 1)) / 1000);
            else d += f
        }
        return d
    },
    getWeekInYear: function () {
        return Math.floor(this.getDayInYear() / 7)
    },
    getDayInYear: function () {
        return Math.floor(this.getHourInYear() / 24)
    },
    getHourInYear: function () {
        return Math.floor(this.getMinuteInYear() / 60)
    },
    getMinuteInYear: function () {
        return Math.floor(this.getSecondInYear() / 60)
    },
    getSecondInYear: function () {
        return Math.floor(this.getMillisecondInYear() / 1000)
    },
    getMillisecondInYear: function () {
        return this.timeDifference(new Date(this.getFullYear(), 0, 1))
    },
    getWeekSince: function (a) {
        return Math.floor(this.getDaySince(a) / 7)
    },
    getDaySince: function (a) {
        return Math.floor(this.getHourSince(a) / 24)
    },
    getHourSince: function (a) {
        return Math.floor(this.getMinuteSince(a) / 60)
    },
    getMinuteSince: function (a) {
        return Math.floor(this.getSecondSince(a) / 60)
    },
    getSecondSince: function (a) {
        return Math.floor(this.getMillisecondSince(a) / 1000)
    },
    getMillisecondSince: function (a) {
        return this.timeDifference(a)
    },
    timeDifference: function (a) {
        return this.getTime() - a.getTime()
    },
    toSwatchInternetTime: function () {
        var a = (this.getHours() * 3600) + (this.getMinutes() * 60) + this.getSeconds() + ((this.getTimezoneOffset() + 60) * 60);
        var b = Math.floor(a / 86.4);
        return ("@" + b)
    },
    fromSwatchInternetTime: function (a) {
        if ($type(a) == "string") a = a.replace("@", "").toInt();
        var b = Math.floor(a * 86.4) - ((this.getTimezoneOffset() + 60) * 60);
        var c = new Date(this.getFullYear(), this.getMonth(), this.getDate());
        c.setTime(c.getTime() + (b * 1000));
        return c
    }
};
try {
    $native(Date);
    Date.extend(_0)
} catch (e) {
    Native.implement([Date], _0)
}
delete _0;
var Calendar = new Class({
    options: {
        visible: false,
        offset: {
            x: 0,
            y: 0
        },
        dateFormat: 'D, d M Y',
        numMonths: 1,
        classPrefix: 'ng-',
        idPrefix: 'ng-',
        startDay: 0,
        startDate: 'today',
        endDate: 'year+10',
        inputType: 'text',
        inputField: null,
        allowSelection: true,
        multiSelection: false,
        maxSelection: 0,
        selectedDate: null,
        datesOff: [],
        allowDatesOffSelection: false,
        daysOff: [],
        allowDaysOffSelection: false,
        weekend: [0, 6],
        allowWeekendSelection: false,
        forceSelections: [],		
        onSelect: function () {
		//alert("on select==="+document.getElementById("date3").value+"");
            return imagePreview1();
        },
        onUnSelect: function () {
			//alert("on un select==="+document.getElementById("date3").value+"");
           return imagePreview1();
        },
        onCalendarLoad: function () {
            return
        },
        onOpen: function () {
            return
        },
        onClose: function () {
            return
        },
        onClear: function () {			
             return clearAll();
        },
        formatter: function (a) {
            return a.getDate()
        },
        outOfRangeFormatter: function (a) {
            return a.getDate()
        },
        weekendFormatter: function (a) {
            return a.getDate()
        },
        daysOffFormatter: function (a) {
            return a.getDate()
        },
        datesOffFormatter: function (a) {
            return a.getDate()
        },
        selectedDateFormatter: function (a) {
            return a.getDate()
        },
        language: null,
        daysText: 'mid',
        monthsText: 'long',
        preTdHTML: "&laquo;",
        preTdHTMLOff: "&nbsp;",
        nexTdHTML: "&raquo;",
        nexTdHTMLOff: "&nbsp;",
        closeLinkHTML: "Close",
        clearLinkHTML: "Clear",
        calEvents: {
            mouseenter: function (a) {
                if (!a.hasClass(this.options.classPrefix + "selected-day")) a.addClass(this.options.classPrefix + "mouse-over")
            },
            mouseleave: function (a) {
                a.removeClass(this.options.classPrefix + "mouse-over")
            }
        },
        tdEvents: [],
        dateOnAvailable: [],
        speedFireFox: false,
        closeOpenCalendars: true
    },
    initialize: function (c, d, f) {
        this.element = $(c);
        if ($defined(d)) this.toggler = $(d);
        else this.toggler = null;
        this.visibleMonth = [];
        this.manageTDs = [];
        this.selectedDates = [];
        this.setOptions(f);
        this.options.inputType = this.options.inputType.toLowerCase();
        this.date = new Date();
        this.date = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate());
        if ($defined(this.options.language)) this.date.language = this.options.language;
        this.options.startDate = this.processDates(this.options.startDate);
        this.options.endDate = this.processDates(this.options.endDate);
        if (!$defined(this.options.startDate)) this.options.startDate = this.date.fromObject({
            date: "year-10"
        });
        else this.date = this.processDates(new Date(this.options.startDate.getTime()));
        if (!$defined(this.options.endDate)) this.options.endDate = this.date.fromObject({
            date: "year+10"
        });
        this.options.selectedDate = this.processDates(this.options.selectedDate);
        if (!this.isSelectable(this.options.selectedDate)) this.options.selectedDate = null;
        this.options.selectedDate2 = this.processDates(this.options.selectedDate2);
        if (!this.isSelectable(this.options.selectedDate2)) this.options.selectedDate2 = null;
        if (!this.options.visible) {
            if ((window.ie) && (!window.ie7)) {
                this.iframe = new Element("iframe", {
                    'src': 'about:Blank',
                    'styles': {
                        'position': 'absolute',
                        'z-index': 20000,
                        'opacity': 0,
                        'background-color': '#ffffff'
                    },
                    'frameborder': 0
                });
                document.body.appendChild(this.iframe)
            }
            this.element.setStyles({
                'position': 'absolute',
                'z-index': 25000,
                'opacity': 0
            });
            if ($defined(this.toggler)) {
                this.toggler.addEvent("click", function (a) {
                    var a = new Event(a);
                    if (this.element.getStyle('opacity') == 0) this.openCalendar();
                    else this.closeCalendar();
                    a.stop()
                }.bind(this))
            }
        }
        if (this.options.numMonths > 1) {
            this.loading_div = new Element("div", {
                'styles': {
                    'position': 'absolute',
                    'z-index': 26000,
                    'opacity': 0,
                    'background': '#FFFFFF'
                }
            });
            this.element.adopt(this.loading_div)
        }
        var g = new Element("table", {
            'class': this.options.classPrefix + 'cal-header-table'
        });
        var j = new Element("tbody");
        g.adopt(j);
        var l = new Element("tr");
        this.preTD = new Element("td", {
            'class': this.options.classPrefix + 'cal-previous-td'
        });
        this.preTD.addEvent("click", function () {
            var a = this.options.numMonths;
            var b;
            while (a > 0) {
                b = this.date.fromString("month-" + a);
                b.setDate(b.daysInMonth());
                if (!this.isOutOfRange(b)) {
                    b.fromString("month-1");
                    break
                }
                a--
            }
            if (a > 0) this.updateCalendar(b)
        }.bind(this));
        l.adopt(this.preTD);
        this.headerTD = new Element("td", {
            'class': this.options.classPrefix + 'cal-header-td'
        });
        l.adopt(this.headerTD);
        this.nexTD = new Element("td", {
            'class': this.options.classPrefix + 'cal-next-td'
        });
        this.nexTD.addEvent("click", function () {
            var a = this.options.numMonths;
            var b;
            while (a > 0) {
                b = this.date.fromString("month+" + a);
                b.setDate(1);
                if (!this.isOutOfRange(b)) {
                    b.fromString("month-1");
                    break
                }
                a--
            }
            if (a > 0) this.updateCalendar(b)
        }.bind(this));
        l.adopt(this.nexTD);
        this.updateHeader();
        j.adopt(l);
        this.element.adopt(g);
        this.calendarHolder = new Element("div");
        this.element.adopt(this.calendarHolder);
        var n = new Element("div", {
            'styles': {
                'clear': 'both'
            }
        });
        this.element.adopt(n);
        var m = new Element("div", {
            'styles': {
                'clear': 'both',
                'height': 1,
                'font-size': '1px'
            }
        });
        m.setHTML("&nbsp;");
        this.element.adopt(m);
        if (!this.options.visible) {
            var k = new Element("a", {
                'class': this.options.classPrefix + 'close-link',
                'href': '#'
            });
            k.addEvent("click", function (a) {
                var a = new Event(a);
                a.preventDefault();
                this.closeCalendar()
            }.bind(this));
            k.setHTML(this.options.closeLinkHTML);
            n.adopt(k)
        }
        if (this.options.multiSelection) {
            var o = new Element("a", {
                'class': this.options.classPrefix + 'clear-link',
                'href': '#'
            });
            o.addEvent("click", function (a) {
                var a = new Event(a);
                a.preventDefault();
                this.unselectAll()
            }.bind(this));
            o.setHTML(this.options.clearLinkHTML);
            n.adopt(o)
        }
        this.populateCalendar();
        if (this.options.allowSelection) {
            if (this.options.inputType == "select") {
                this.options.inputField.year = $(this.options.inputField.year);
                this.options.inputField.month = $(this.options.inputField.month);
                this.options.inputField.date = $(this.options.inputField.date);
                this.options.inputField.year.addEvent("change", function () {
                    if (this.options.inputField.year.options[this.options.inputField.year.selectedIndex].value != "") {
                        if ($defined(this.options.selectedDate)) var a = new Date(this.options.selectedDate.getTime());
                        else var a = new Date(this.date.getTime());
                        a.setYear(this.options.inputField.year.options[this.options.inputField.year.selectedIndex].value);
                        if (!this.options.multiSelection) this.selectDate(a);
                        this.updateCalendar(a);
                        this.populateMonthSelect()
                    }
                }.bind(this));
                this.options.inputField.month.addEvent("change", function () {
                    if (this.options.inputField.month.options[this.options.inputField.month.selectedIndex].value != "") {
                        if ($defined(this.options.selectedDate)) var a = new Date(this.options.selectedDate.getTime());
                        else var a = new Date(this.date.getTime());
                        var b = a.getDate();
                        a.setDate(1);
                        a.setMonth(this.options.inputField.month.options[this.options.inputField.month.selectedIndex].value.toInt() - 1);
                        if (a.daysInMonth() > b) a.setDate(b);
                        else a.setDate(a.daysInMonth());
                        if (!this.options.multiSelection) this.selectDate(a);
                        if (!$defined(this.visibleMonth[a.getMonth() + "-" + a.getFullYear()])) this.updateCalendar(a);
                        this.populateDateSelect(this.options.inputField)
                    }
                }.bind(this));
                this.options.inputField.date.addEvent("change", function () {
                    if ($defined(this.options.selectedDate)) var a = new Date(this.options.selectedDate.getTime());
                    else var a = new Date(this.date.getTime());
                    a.setDate(this.options.inputField.date.options[this.options.inputField.date.selectedIndex].value);
                    this.selectDate(a)
                }.bind(this));
                this.populateSelect()
            } else if (this.options.inputType == "text") {
                this.options.inputField = $(this.options.inputField);
                this.options.inputField.addEvent("focus", function () {
                    this.openCalendar()
                }.bind(this));
                this.options.inputField.addEvent("keydown", function (a) {
                    var a = new Event(a);
                    if ((a.key.length == 1) || (a.key == "space")) a.stop()
                })
            }
        }
        if ($defined(this.options.selectedDate)) {
            if ((window.ie6) && (this.options.inputType == "select")) {
                (function () {
                    this.selectDate(this.options.selectedDate);
                    this.updateCalendar(this.options.selectedDate)
                }).delay(100, this)
            } else {
                this.selectDate(this.options.selectedDate);
                this.updateCalendar(this.options.selectedDate)
            }
        }
        _1.push(this)
    },
    populateSelect: function () {
        if (this.options.inputType != "select") return;
        this.options.inputField.year.empty();
        var a = new Element("option");
        this.options.inputField.year.adopt(a);
        var b = 0;
        for (b = this.options.startDate.getFullYear(); b <= this.options.endDate.getFullYear(); b++) {
            a = new Element("option", {
                'value': b
            });
            a.setText(b);
            if (($defined(this.options.selectedDate)) && (this.options.selectedDate.getFullYear() == b)) a.selected = "selected";
            this.options.inputField.year.adopt(a)
        }
        this.populateMonthSelect()
    },
    populateMonthSelect: function () {
        if (this.options.inputType != "select") return;
        var a = 0;
        if (this.options.startDate.getFullYear() == this.date.getFullYear()) a = this.options.startDate.getMonth();
        var b = 11;
        if (this.options.endDate.getFullYear() == this.date.getFullYear()) b = this.options.endDate.getMonth();
        this.options.inputField.month.empty();
        opt = new Element("option");
        this.options.inputField.month.adopt(opt);
        for (i = a; i <= b; i++) {
            opt = new Element("option", {
                'value': (i + 1)
            });
            opt.setText(this.date.language.months[this.options.monthsText][i]);
            if (($defined(this.options.selectedDate)) && (this.options.selectedDate.getMonth() == i)) opt.selected = "selected";
            this.options.inputField.month.adopt(opt)
        }
        this.populateDateSelect()
    },
    populateDateSelect: function () {
        if (this.options.inputType != "select") return;
        if ((this.options.inputField.year.options[this.options.inputField.year.selectedIndex].value != "") && (this.options.inputField.month.options[this.options.inputField.month.selectedIndex].value != "")) var a = new Date(this.options.inputField.year.options[this.options.inputField.year.selectedIndex].value, this.options.inputField.month.options[this.options.inputField.month.selectedIndex].value - 1, 1);
        else if ($defined(this.options.selectedDate)) var a = this.options.selectedDate;
        else var a = this.date;
        var b = a.daysInMonth();
        this.options.inputField.date.empty();
        opt = new Element("option");
        this.options.inputField.date.adopt(opt);
        var c;
        for (i = 1; i <= b; i++) {
            opt = new Element("option", {
                'value': i
            });
            opt.setText(i);
            if (!this.isSelectable(new Date(a.getFullYear(), a.getMonth(), i))) {
                opt.disabled = true;
                opt.setStyles({
                    'color': '#cccccc'
                })
            } else if (($defined(this.options.selectedDate)) && (this.options.selectedDate.getDate() == i) && (this.options.selectedDate.getMonth() == this.options.inputField.month.options[this.options.inputField.month.selectedIndex].value - 1) && (this.options.selectedDate.getFullYear() == this.options.inputField.year.options[this.options.inputField.year.selectedIndex].value)) opt.selected = "selected";
            this.options.inputField.date.adopt(opt)
        }
    },
    populateCalender: function () {
        return this.populateCalendar()
    },
    populateCalendar: function () {
        var d = function (a, b) {
            var c = 0;
            this.visibleMonth = [];
            this.calendarHolder.setHTML("");
            for (c = 0; c < this.options.numMonths; c++) {
                this.calendarHolder.innerHTML += this.createCalenderTable(a, b);
                this.visibleMonth[a + "-" + b] = true;
                a++;
                if (a > 11) {
                    a = 0;
                    b++
                }
            }
            if (this.options.numMonths > 1) {
                this.loading_div.setOpacity(0)
            }
            this.processTdEvents();
            if ($defined(this.iframe)) {
                this.iframe.setStyles({
                    'width': this.element.getStyle('width'),
                    'height': this.element.getStyle('height')
                })
            }
            this.fireEvent("onCalendarLoad")
        };
        if (this.options.numMonths > 1) {
            if (this.element.getStyle('opacity') > 0) {
                this.loading_div.setStyles({
                    'opacity': 0.5,
                    'height': this.element.getStyle('height'),
                    'width': this.element.getStyle('width')
                })
            }
            d.delay(1, this, [this.date.getMonth(), this.date.getFullYear()])
        } else {
            this.visibleMonth = [];
            this.calendarHolder.innerHTML = this.createCalenderTable(this.date.getMonth(), this.date.getFullYear());
            this.visibleMonth[this.date.getMonth() + "-" + this.date.getFullYear()] = true;
            this.processTdEvents();
            if ($defined(this.iframe)) {
                this.iframe.width = this.element.getStyle('width');
                this.iframe.height = this.element.getStyle('height')
            }
            this.fireEvent("onCalendarLoad")
        }
    },
    createCalenderTable: function (a, b) {	
        var c = new Array();
        c[c.length] = '<table class="' + this.options.classPrefix + 'cal" id="' + this.options.idPrefix + 'month-' + (a + 1) + '-' + b + '-table"><tr>				<th class="' + this.options.classPrefix + 'month-name-th" id="' + this.options.idPrefix + 'month-name-' + (a + 1) + '-' + b + '-th" colspan="7">' + this.date.language.months[this.options.monthsText][a] + " " + b + '</th></tr><tr>';
        var d = 0;
        var f = 0;
        for (d = 0; d < 7; d++) {
            f = (d + this.options.startDay) % 7;
            c[c.length] = '<td class="' + this.options.classPrefix + 'days-name-td" id="' + this.options.idPrefix + 'days-name-' + f + '-' + (a + 1) + '-' + b + '-td">' + this.date.language.days[this.options.daysText][f] + '</td>'
        }
        c[c.length] = '</tr>';
        var g = new Date(b, a, 1);
        g.setDate(g.getDate() - (g.getDay() - this.options.startDay));
        if ((g.getDate() <= 7) && (g.getDate() != 1)) {
            g.setDate(g.getDate() - 7)
        }
        var d, j, l, n, m, k, o;
        var q = 7;
        for (d = 1; d < q; d++) {
            c[c.length] = '<tr>';
            for (j = 1; j <= 7; j++) {
                l = "";
                m = "";
                k = "";
                if (g.getMonth() != a) {
                    k = (g.getMonth() + 1) + '-' + g.getDate() + '-' + g.getFullYear();
                    c[c.length] = '<td class="' + this.options.classPrefix + 'date-' + k + ' ' + this.options.classPrefix + 'outOfRange">' + this.options.outOfRangeFormatter(g) + '</td>'
                } else {
                    o = this.isSelectable(g, true);
                    if (o[1] == "outOfRange") {
                        l = this.options.classPrefix + "outOfRange";
                        m = this.options.outOfRangeFormatter(g)
                    } else if (o[1] == "weekend") {
                        l = this.options.classPrefix + "weekend";
                        m = this.options.weekendFormatter(g)
                    } else if (o[1] == "dayOff") {
                        l = this.options.classPrefix + "dayOff";
                        m = this.options.daysOffFormatter(g)
                    } else if (o[1] == "dateOff") {
                        l = this.options.classPrefix + "dateOff";
                        m = this.options.datesOffFormatter(g)
                    } else {
                        m = this.options.formatter(g)
                    }
                    if (o[0]) {
                        if (this.isSelected(g)) {
                            l += " " + this.options.classPrefix + "selected-day";
                            m = this.options.selectedDateFormatter(g)
                        }
                    }
                    k = (g.getMonth() + 1) + '-' + g.getDate() + '-' + g.getFullYear();
                    this.manageTDs[k] = [];
                    c[c.length] = '<td class="' + this.options.classPrefix + 'date-' + k + ' ' + l + '" id="' + this.options.idPrefix + 'date-' + k + '">' + m + '</td>';
                    if (o[0]) this.manageTDs[k]['click'] = true;
                    if ($defined(this.options.tdEvents[k])) {
                        if (!$defined(this.manageTDs[k]['event'])) this.manageTDs[k]['event'] = [];
                        for (e in this.options.tdEvents[k]) {
                            this.manageTDs[k]['event'][e] = this.options.tdEvents[k][e]
                        }
                    }
                    if ($defined(this.options.dateOnAvailable[k])) {
                        if (!$defined(this.manageTDs[k]['dateOnAvailable'])) this.manageTDs[k]['dateOnAvailable'] = [];
                        this.manageTDs[k]['dateOnAvailable'].push(this.options.dateOnAvailable[k])
                    }
                }
                g.setDate(g.getDate() + 1)
            }
            c[c.length] = '</tr>';
            if ((g.getMonth() > a) && (this.options.numMonths == 1)) q = 6
        }
        c[c.length] = '</table>';
        return c.join("")
    },
    processTdEvents: function () {
        var d, f;
        for (p in this.manageTDs) {
            f = this.manageTDs[p];
            d = $(this.options.idPrefix + 'date-' + p);
            var g = p;
            if ($defined(f['click'])) {
                d.addEvent("click", function (a, b) {
                    var c = new Date().fromString(b);
					
                    if (this.isSelected(c))
					{
						this.unselectDate(c);
					}
                    else this.selectDate(c)
                }.bind(this, [d, g]));
                d.setStyle("cursor", "pointer");
                for (e in this.options.calEvents) d.addEvent(e, this.options.calEvents[e].bind(this, d))
            }
            if ($defined(f['event'])) {
                for (ep in f['event']) {
                    if ($type(f['event'][ep]) == "function") {
                        d.addEvent(ep, f['event'][ep].bind(this, [d, g]))
                    }
                }
            }
            if ($defined(f['dateOnAvailable'])) {
                f['dateOnAvailable'].each(function (a) {
                    a.attempt([d, g], this)
                }, this)
            }
        }
        this.manageTDs = []
    },
    isWeekend: function (a) {
        return this.options.weekend.contains(a)
    },
    isDayOff: function (a) {
        if ((this.options.speedFireFox) && (window.gecko)) return false;
        return this.options.daysOff.contains(a)
    },
    isDateOff: function (a) {
        if ((this.options.speedFireFox) && (window.gecko)) return false;
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        var b = 0;
        var c = this.options.datesOff.length;
        for (b = 0; b < c; b++) {
            cur_date = this.processDates(this.options.datesOff[b], a);
            if ($defined(cur_date)) if (a.getTime() == cur_date.getTime()) return true
        }
        return false
    },
    isOutOfRange: function (a) {
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        return ((a.getTime() < this.options.startDate.getTime()) || (a.getTime() > this.options.endDate.getTime()))
    },
    isSelectable: function (a, b) {
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        if ((!$defined(b)) && (!this.options.allowSelection)) return false;
        var c = [true, a];
        if (this.isOutOfRange(a)) c = [false, 'outOfRange'];
        else if (this.isDayOff(a.getDay())) c = [this.options.allowDaysOffSelection, 'dayOff'];
        else if (this.isWeekend(a.getDay())) c = [this.options.allowWeekendSelection, 'weekend'];
        else if (this.isDateOff(a)) c = [this.options.allowDatesOffSelection, 'dateOff'];
        if ((!this.options.allowSelection) && (c[0])) c = [false, 'noSelection'];
        if (!c[0]) if (this.isForcedSelection(a)) c = [true, a];
        if ($defined(b)) return c;
        else return c[0];
        return [false, a]
    },
    isForcedSelection: function (a) {
        if ((this.options.speedFireFox) && (window.gecko)) return false;
        var b = 0;
        var c = this.options.forceSelections.length;
        var d;
        for (b = 0; b < c; b++) {
            d = this.processDates(this.options.forceSelections[b], a);
            if (d.getTime() == a.getTime()) return true
        }
        return false
    },
    isSelected: function (a) {
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        return this.selectedDates.contains(a.getTime())
    },
    updateCalendar: function (a) {
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        if (this.isOutOfRange(a)) this.date = this.options.startDate;
        else this.date = a;
        this.populateCalendar();
        this.updateHeader()
    },
    selectDate: function (a) {
        if (!this.options.allowSelection) return false;
        var a = this.processDates(a);
        if (!$defined(a)) return false;
        if ((this.options.maxSelection > 0) && (this.selectedDates.length >= this.options.maxSelection)) return false;
        if (this.isSelectable(a)) {
            if (this.options.inputType == "select") {
                var b = true;
                var c = true;
                if ($defined(this.options.selectedDate)) {
                    if (a.getFullYear() == this.options.selectedDate.getFullYear()) b = false;
                    else if (a.getMonth() == this.options.selectedDate.getMonth()) c = false
                }
            }
            if (!this.options.multiSelection) this.unselectDate(this.options.selectedDate);
            this.options.selectedDate = this.date = a;
            this.selectedDates.push(a.getTime());
            var d = this.options.idPrefix + 'date-' + (a.getMonth() + 1) + '-' + a.getDate() + '-' + a.getFullYear();
            if ($defined($(d))) {
                $(d).removeClass(this.options.classPrefix + "mouse-over");
                $(d).addClass(this.options.classPrefix + "selected-day");
                $(d).setHTML(this.options.selectedDateFormatter(this.options.selectedDate))
            }
            if (this.options.inputType == "select") {
                if (b) this.populateMonthSelect();
                else if (c) this.populateDateSelect();
                this.selectSelectMenu()
            } else if (this.options.inputType == "text") this.fillInputField();
            this.fireEvent("onSelect");
            return true
        } else {
            if (this.options.inputType == "select") {
                this.options.inputField.date.selectedIndex = 0;
                return false
            }
        }
        this.updateHeader()
    },
    unselectDate: function (a) {
        if (!this.options.allowSelection) return false;
        var a = this.processDates(a);
        if (!$defined(a)) return false;
		
        this.selectedDates.remove(a.getTime());
        var b = this.options.idPrefix + 'date-' + (a.getMonth() + 1) + '-' + a.getDate() + '-' + a.getFullYear();
        if ($defined($(b))) $(b).removeClass(this.options.classPrefix + "selected-day");
		
		
        if (($defined(this.options.selectedDate)) && (this.options.multiSelection)) {
            if (this.options.selectedDate.getTime() == a.getTime()) {
                if ($defined(this.selectedDates.getLast())) {
                    this.options.selectedDate = new Date(this.selectedDates.getLast())
                } else this.options.selectedDate = null
            }
        } else this.options.selectedDate = null;
        if (this.options.inputType == "select") {
            var c = true;
            var d = true;
            if ($defined(this.options.selectedDate)) {
                if (a.getFullYear() == this.options.selectedDate.getFullYear()) c = false;
                else if (a.getMonth() == this.options.selectedDate.getMonth()) d = false
            }
            if (c) this.populateMonthSelect();
            else if (d) this.populateDateSelect();
            this.selectSelectMenu()
        } else if (this.options.inputType == "text") this.options.inputField.value = this.options.inputField.value.replace(a.print(this.options.dateFormat, this.options.language), "");
        this.fireEvent("onUnSelect", a)
    },
    unselectAll: function () {
        if (!this.options.allowSelection) return false;
        var b = this.selectedDates.copy();
        b.each(function (a) {		
            this.unselectDate(new Date(a))
        }, this);
        this.fireEvent("onClear")
    },
    selectSelectMenu: function () {
        if (this.options.inputType != "select") return;
        if (!$defined(this.options.selectedDate)) {
            this.options.inputField.date.selectedIndex = 0;
            this.options.inputField.month.selectedIndex = 0;
            this.options.inputField.year.selectedIndex = 0;
            return
        }
        this.options.inputField.date.selectedIndex = this.options.selectedDate.getDate();
        var a;
        var b = 0;
        var c = $(this.options.inputField.month).getElements("option");
        a = c.length;
        for (b = 0; b < a; b++) {
            if (c[b].value.toInt() - 1 == this.options.selectedDate.getMonth()) {
                this.options.inputField.month.selectedIndex = b;
                break
            }
        }
        var c = $(this.options.inputField.year).getElements("option");
        a = c.length;
        for (b = 0; b < a; b++) {
            if (c[b].value.toInt() == this.options.selectedDate.getFullYear()) {
                this.options.inputField.year.selectedIndex = b;
                break
            }
        }
    },
    fillInputField: function () {
        if (!this.options.multiSelection) this.options.inputField.value = "";
        this.options.inputField.value += this.options.selectedDate.print(this.options.dateFormat, this.options.language)
    },
    updateHeader: function () {
        this.headerTD.setHTML(this.date.language.months[this.options.monthsText][this.date.getMonth()] + " " + this.date.getFullYear());
        if (!this.isOutOfRange(new Date(this.date.getFullYear(), this.date.getMonth() - 1, this.options.startDate.getDate()))) {
            this.preTD.setHTML(this.options.preTdHTML);
            this.preTD.setStyle("cursor", "pointer")
        } else {
            this.preTD.setHTML(this.options.preTdHTMLOff);
            this.preTD.setStyle("cursor", "")
        }
        if (!this.isOutOfRange(new Date(this.date.getFullYear(), this.date.getMonth() + 1, this.options.endDate.getDate()))) {
            this.nexTD.setHTML(this.options.nexTdHTML);
            this.nexTD.setStyle("cursor", "pointer")
        } else {
            this.nexTD.setHTML(this.options.nexTdHTMLOff);
            this.nexTD.setStyle("cursor", "")
        }
    },
    processDates: function (a, b) {
        if (!$defined(b)) b = this.date;
        var c;
        if ((($type(a) == "object") && ($defined(a.getFullYear))) || ($type(a) == "date")) c = a;
        else if ($type(a) == "object") c = b.fromObject(a);
        else if ($type(a) == "string") c = b.fromString(a);
        else if ($type(a) == "number") c = new Date(a);
        else return null;
        if ($defined(this.options.language)) c.language = this.options.language;
        c.setHours(0);
        c.setSeconds(0);
        c.setMinutes(0);
        c.setMilliseconds(0);
        return c
    },
    openCalendar: function () {
        if (this.options.closeOpenCalendars) $closeAllCalendars();
        if (this.options.inputType == "select") var a = this.options.inputField.date;
        else var a = this.options.inputField;
        var b = a.getCoordinates();
        this.element.setStyles({
            'top': b.top + b.height + this.options.offset.y,
            'left': b.left + this.options.offset.x,
            'opacity': 1
        });
        if ($defined(this.iframe)) {
            this.iframe.setStyles({
                'top': b.top + b.height + this.options.offset.y,
                'left': b.left + this.options.offset.x,
                'opacity': 1
            })
        }
        this.fireEvent("onOpen")
    },
    closeCalendar: function () {
        if (this.options.visible) return false;
        this.element.setStyle('opacity', 0);
        if ($defined(this.iframe)) this.iframe.setStyle('opacity', 0);
        this.fireEvent("onClose")
    }
});
Calendar.implement(new Events, new Options);
var _1 = [];
var $closeAllCalendars = function () {
    _1.each(function (a) {
        a.closeCalendar()
    })
};