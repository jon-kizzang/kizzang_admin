Alter table kizzang.GameExpireTimes add lowAmount decimal(10,2);
Alter table kizzang.GameExpireTimes add highAmount decimal(10,2);

Truncate GameExpireTimes;
Insert into GameExpireTimes (game, numMinutes, lowAmount, highAmount) values 
('slotTournament', 1440, 0, 599),
('slotTournament', 2880, 600, 24999),
('slotTournament', 7200, 25000, 1000000),
('finalThree', 1440, 0, 599),
('finalThree', 2880, 600, 24999),
('finalThree', 7200, 25000, 1000000),
('bigGame', 1440, 0, 599),
('bigGame', 2880, 600, 24999),
('bigGame', 7200, 25000, 1000000),
('dailyShowdown', 1440, 0, 599),
('dailyShowdown', 2880, 600, 24999),
('dailyShowdown', 7200, 25000, 1000000),
('sweepstakes', 1440, 0, 599),
('sweepstakes', 2880, 600, 24999),
('sweepstakes', 7200, 25000, 1000000),
('scratchCard', 60, 0, 599),
('scratchCard', 2880, 600, 24999),
('scratchCard', 7200, 25000, 1000000);