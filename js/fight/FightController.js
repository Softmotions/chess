FightController = function(url, fightId, container) {
  this.url = url;
  this.fightId = fightId;
  this.container = container;

  this.board = new Array();
  this.selected = null;

  this.invalidated = false;
};

FightController.main = function(url, fightId, container) {
  window.fc = new FightController(url, fightId, container);
};

FightController.prototype.initMoveData = function(data) {
  var me = this;

  //  if ( data.moves.length > 0 ) {
  var pos = '' + data.x + data.y;
  this.board[ pos ] = data;
  $('item' + pos).onclick = function(event) {
    me.selectItem(data.x, data.y, true);
  };
};

FightController.prototype.markFigure = function(x, y) {
  var me = this;

  $('item' + x + y).style.background = 'yellow';

  for (var i = 0; i < this.board[ '' + x + y ].moves.length; ++i) {
    if (!me.board[ '' + x + y ].moves[ i ]) {
      continue;
    }

    var item = me.board[ '' + x + y ].moves[ i ];
    var color = (item.type == 'move' || item.type == 'smove') ? 'blue' : 'pink';
    var cell = $('item' + item.x + item.y);
    cell.setAttribute('_x', item.x);
    cell.setAttribute('_y', item.y);
    cell.style.background = color;
    cell.onclick = function(_event) {
      var target = (_event && _event.target) || event.srcElement;
      while (target.tagName.toLowerCase() != 'td') {
        target = target.parentNode;
      }

      var _x = target.getAttribute('_x');
      var _y = target.getAttribute('_y');
      me.selectItem(_x, _y, false);
    };
  }
};

FightController.prototype.unmarkFigure = function(x, y) {
  $('item' + x + y).style.background = ( x + y ) % 2 == 0 ? 'brown' : 'white';

  for (var i = 0; i < this.board[ '' + x + y ].moves.length; ++i) {
    if (!this.board[ '' + x + y ].moves[ i ]) {
      continue;
    }

    var item = this.board[ '' + x + y ].moves[ i ];
    var cell = $('item' + item.x + item.y);
    cell.style.background = ( item.x + item.y ) % 2 == 0 ? 'brown' : 'white';
    cell.onclick = function(){};
  }
};

FightController.prototype.selectItem = function(x, y, figure) {
  if (this.invalidated) {
    return;
  }

  var rx;
  var ry;

  if (figure) {
    if (!this.board[ '' + x + y ]) {
      return;
    }

    if (this.selected) {
      rx = this.selected.x;
      ry = this.selected.y;
      this.unmarkFigure(rx, ry);
      this.selected = null;
    }

    if (rx == x && ry == y) {
      return;
    }

    this.selected = { x: x, y: y };
    this.markFigure(x, y);
    return;
  }

  var move = null;

  rx = this.selected.x;
  ry = this.selected.y;
  for (var i = 0; i < this.board[ '' + rx + ry ].moves.length; ++i) {
    if (!this.board[ '' + rx + ry ].moves[ i ]) {
      continue;
    }

    var item = this.board[ '' + rx + ry ].moves[ i ];
    if (item.x == x && item.y == y) {
      move = item;
    }
  }

  if (move) {
    this.invalidated = true;
    this.unmarkFigure(rx, ry);
    this.selected = null;

    var rpawns = document.getElementsByName('rpawn');
    var rpawn;
    if (rpawns) {
      rpawn = rpawns[0];
    }

    rqd.doGateWayRequest(
        this.url,
        move.type,
        { sx: rx, sy: ry, tx: move.x, ty: move.y, rpawn: rpawn.value, container: this.container, fightId: this.fightId }
        );
  } else {
    if (( x != this.selected.x || y != this.selected.y ) && this.board[ '' + x + y ]) {
      rx = this.selected.x;
      ry = this.selected.y;
      this.unmarkFigure(rx, ry);

      this.selected = { x: x, y: y };
      this.markFigure(x, y)
    }
  }
};
