const boardSpace = document.querySelector("#board");

const boardContainer = document.createElement('div');
boardContainer.className = 'container-fluid';
boardContainer.classList.add("board-container");


for(let i = 0 ; i < 7 ; i++){
    const boardRow = document.createElement('div');
    boardRow.classList.add('row');
    for(let j = 0; j < 7 ; j++){
        let room = document.createElement('div');
        room.classList.add('col-sm');
        room.classList.add('room');

        boardRow.appendChild(room);
    }
    boardContainer.appendChild(boardRow);
}

boardSpace.appendChild(boardContainer);