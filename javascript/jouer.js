const rules = {
  Monopoly: [
    "Goal: Be the last player not bankrupt",
    "Start with $1500 each",
    "Roll dice and move around the board",
    "Buy unowned properties or auction them",
    "Pay rent when landing on others' properties",
    "Collect $200 when passing GO",
    "Build houses and hotels on full color sets",
    "Go to jail if instructed",
    "Lose when you cannot pay debts"
  ],

  ExplodingKittens: [
    "Goal: Avoid exploding and be the last alive",
    "Each player starts with 7 cards and 1 Defuse",
    "On your turn: play cards or pass",
    "You must draw a card at the end of your turn",
    "If you draw an Exploding Kitten, you lose",
    "Use Defuse to survive an explosion",
    "Use action cards to skip, attack, or shuffle",
    "Game ends when only one player remains"
  ],

  Uno: [
    "Players: 2–10",
    "Goal: Get rid of all your cards",
    "Each player starts with 7 cards",
    "Match cards by color, number, or symbol",
    "If no match, draw a card",
    "Skip card: next player loses turn",
    "Reverse card: change direction",
    "+2 card: next player draws 2 and skips",
    "Wild card: choose a color",
    "Wild +4: draw 4 and choose color",
    "Say 'UNO' when you have one card left",
    "First player with 0 cards wins"
  ],

  Chess: [
    "Players: 2",
    "Goal: Checkmate the opponent’s king",
    "Game is played on an 8x8 board",
    "Each player starts with 16 pieces",
    "Pawns move forward, capture diagonally",
    "Rooks move in straight lines",
    "Bishops move diagonally",
    "Knights move in L-shapes",
    "Queen moves in all directions",
    "King moves one square in any direction",
    "Special moves: castling, en passant, promotion",
    "Check: king is under attack",
    "Checkmate: king cannot escape (win)",
    "Stalemate: no legal moves (draw)"
  ],
  Werewolf: [
    "Goal: Villagers eliminate all werewolves; Werewolves eliminate villagers",
    "Roles assigned secretly: Villagers, Werewolves, Seer, etc.",
    "Game alternates between Night and Day phases",
    "At Night: Werewolves secretly choose a victim",
    "During Day: Players discuss and vote to eliminate a suspected werewolf",
    "Special roles have unique abilities",
    "Game ends when one team achieves their goal"
  ],

  Cluedo: [
    "Goal: Solve the murder by identifying suspect, weapon, and room",
    "Each player starts with cards that eliminate possibilities",
    "On your turn: move, make a suggestion about the murder",
    "Other players must disprove your suggestion if they can",
    "Use deduction to narrow down the murderer, weapon, and room",
    "Make an accusation when confident",
    "Win by correctly accusing; lose if wrong"
  ],

  Catan: [
    "Goal: Be the first to reach 10 victory points",
    "Players collect resources: brick, wood, sheep, wheat, ore",
    "Build roads, settlements, and cities using resources",
    "Trade resources with players or the bank",
    "Roll dice to collect resources based on settlements",
    "Use development cards for special actions",
    "Game ends when a player reaches 10 points"
  ],

  WhoIsIt: [
    "Goal: Guess the opponent's chosen character first",
    "Each player chooses a character card secretly",
    "Players take turns asking yes/no questions about characteristics",
    "Eliminate possibilities based on answers",
    "First to correctly guess opponent’s character wins"
  ],

  JungleSpeed: [
    "Goal: Get rid of all your cards first",
    "Players take turns revealing cards in a pile",
    "When two cards match, players grab the totem",
    "Fastest player to grab correctly wins the pile; loser takes cards",
    "Special cards change the rules temporarily",
    "Game continues until one player has no cards left"
  ],
  CardsAgainstHumanity: [
    "Goal: Create the funniest or most outrageous answer combinations",
    "Each round has a Card Czar who draws a black card (prompt)",
    "Other players submit white cards (answers) anonymously",
    "Card Czar picks the funniest or most fitting white card",
    "Winner of the round gets 1 point",
    "Game continues until players decide to stop"
  ],
  TruthOrDare: [
    "Goal: Have fun by completing dares or answering truthfully",
    "Players take turns choosing 'Truth' or 'Dare'",
    "Truth: answer a question honestly",
    "Dare: complete a challenge given by others",
    "Refusing usually has a penalty (optional)",
    "Game continues as long as players want"
  ],
  Coup: [
    "Goal: Be the last player with influence",
    "Each player has 2 face-down character cards",
    "On your turn: take income, foreign aid, or perform character action",
    "Players can challenge or block actions",
    "Lose a card if challenged or targeted successfully",
    "Game ends when only one player has remaining influence"
  ],
  Charades: [
    "Goal: Have your team guess the word or phrase correctly",
    "Divide into teams",
    "One player acts out a word/phrase without speaking",
    "Team guesses within a time limit",
    "Correct guesses score points",
    "Alternate turns; team with most points wins"
  ],
  Skyjo: [
    "Goal: Have the lowest total score after several rounds",
    "Each player has a 4x4 grid of face-down cards",
    "On your turn: draw a card from the deck or discard pile",
    "Replace one of your cards or discard it",
    "Reveal cards strategically to minimize your score",
    "Round ends when a player reveals all their cards",
    "Sum your cards; lowest total wins the round",
    "Game ends after a set number of rounds"
  ],

  Dobble: [
    "Goal: Be the first to spot matching symbols between two cards",
    "Each card has 8 symbols; exactly one symbol matches any other card",
    "Players reveal cards simultaneously or follow the chosen variant",
    "Spot the match and call it out quickly",
    "The fastest player collects the cards",
    "Game ends when the deck is finished or a player reaches the winning condition"
  ],

  Memory: [
    "Goal: Collect the most matching pairs",
    "All cards are placed face down",
    "Players take turns flipping two cards",
    "If they match, the player keeps the pair and plays again",
    "If not, flip them back and the next player goes",
    "Game ends when all pairs are collected",
    "Player with the most pairs wins"
  ],

  Jenga: [
    "Goal: Remove and stack blocks without toppling the tower",
    "Stack all wooden blocks to form a tower",
    "Players take turns removing one block from any level (except the top)",
    "Place the removed block on top of the tower",
    "Game continues until the tower falls",
    "Player who causes the tower to fall loses"
  ],

  Dominoes: [
    "Goal: Be the first to play all your dominoes or have the fewest points left",
    "Each player draws a hand of dominoes",
    "Players take turns placing matching ends of dominoes on the layout",
    "If a player cannot play, they draw or pass",
    "Round ends when a player finishes their dominoes or no moves are possible",
    "Score points based on remaining tiles or game variant"
  ],

  TimesUp: [
    "Goal: Get your team to guess as many famous names as possible",
    "Divide into teams",
    "One player gives clues without saying the name",
    "Round 1: Describe freely; Round 2: only one word; Round 3: gestures only",
    "Team guesses as many names as possible in the time limit",
    "Rotate roles each round",
    "Team with most correct guesses wins"
  ],

  StandardCards: [
    "Goal depends on chosen game: Poker, Rummy, Solitaire, etc.",
    "Deck has 52 cards (no jokers unless specified)",
    "Shuffle deck and deal according to the game rules",
    "Follow standard rules for drawing, discarding, and scoring",
    "Game ends according to the variant being played",
    "Player with the highest score or best hand wins"
  ],

};

function openRules(gameName) {
  const game = rules[gameName];
  document.getElementById('rulesTitle').innerText = gameName + " Rules";
  if (!game) {
    document.getElementById('rulesText').innerHTML = "<p>Rules not available.</p>";
    return;
  }
  let html = "<ul>";
  game.forEach(rule => { html += `<li>${rule}</li>`; });
  html += "</ul>";
  document.getElementById('rulesText').innerHTML = html;
  document.getElementById('rulesModal').style.display = "block";
}

function closeRules() {
  document.getElementById('rulesModal').style.display = "none";
}

// ⚠️ Attendre que le DOM soit chargé avant d'attacher les événements
document.addEventListener('DOMContentLoaded', function() {
  const bookButtons = document.querySelectorAll('.btn-reserve');
  console.log("Boutons 'Book' trouvés :", bookButtons.length); // pour debug

  bookButtons.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();  // empêche la navigation par défaut du <a>
      const gameCard = this.closest('.game-card');
      if (!gameCard) {
        alert("Erreur : carte du jeu introuvable.");
        return;
      }
      const gameNameElem = gameCard.querySelector('.game-content h3');
      const gameName = gameNameElem ? gameNameElem.innerText.trim() : null;
      if (gameName) {
        sessionStorage.setItem('activity', gameName);
        console.log("Nom du jeu stocké :", gameName);
        window.location.href = 'reservation.html';
      } else {
        alert("Impossible de récupérer le nom du jeu.");
      }
    });
  });
});