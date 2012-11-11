<?php

/*
	Available variables
	{CREATOR_NAME}
	{USER_NAME}
	{MEETING_NAME}
	{MEETING_RANGE}
	{MEETING_LENGTH}
	{SOLUTION}
*/
class MeetableMessages
{
static $smsMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm-time' => 'You have selected your availability {USER_RANGE} for {MEETING_NAME}. To change this please reply with another time or time range.',
'status' => 'The current time range for {MEETING_NAME} is {MEETING_RANGE}.',
'cancel' => '{MEETING_NAME} has been cancelled by {CREATOR_NAME}',
'solution-found' => 'It has been decided! {MEETING_NAME} will be held on {SOLUTION}. See you there!',
'partially-solved-choice' => 'Everyone has chosen their times for {MEETING_NAME} and we have found a range of times everyone is free. Please choose a time between {MEETING_RANGE} and we will notify everyone.',
'unsolvable-choice' => 'No body was able to agree on a time for {MEETING_NAME}. You may choose any time or cancel (reply KILL) and we will notify everyone.',
'bad-time' => 'The time you sent is not within {MEETING_RANGE} or is in the past. Please try again.',
'bad-input' => 'Sorry, we did not understand your response. Please try again.',
'exceeded-max-meetings' => 'You have exceeded the maximum number of open meeting invites.'
);

static $emailMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm-time' => 'You have selected your availability {USER_RANGE} for {MEETING_NAME}. To change this please reply with another time or time range.',
'status' => 'The current time range for {MEETING_NAME} is {MEETING_RANGE}.',
'cancel' => '{MEETING_NAME} has been cancelled by {CREATOR_NAME}',
'solution-found' => 'It has been decided! {MEETING_NAME} will be held on {SOLUTION}. See you there!',
'partially-solved-choice' => 'Everyone has chosen their times for {MEETING_NAME} and we have found a range of times everyone is free. Please choose a time between {MEETING_RANGE} and we will notify everyone.',
'unsolvable-choice' => 'No body was able to agree on a time for {MEETING_NAME}. You may choose any time or cancel (reply KILL) and we will notify everyone.',
'bad-time' => 'The time you sent is not within the available time range {MEETING_RANGE}. Please try again.',
'bad-input' => 'Sorry, we did not understand your response. Please try again.',
'exceeded-max-meetings' => 'You have exceeded the maximum number of open meeting invites.'
);

static $emailSubjects = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you',
'confirm-time' => 'Confirmation of your time for {MEETING_NAME}',
'status' => 'Status update on {MEETING_NAME}',
'cancel' => '{MEETING_NAME} has been cancelled by {CREATOR_NAME}',
'solution-found' => 'A time has been decided for {MEETING_NAME}!',
'partially-solved-choice' => 'Please choose a time for {MEETING_NAME}',
'unsolvable-choice' => 'Please choose a time for {MEETING_NAME}',
'bad-time' => 'The time you send is invalid',
'bad-input' => 'We had trouble understanding your reply.',
'exceeded-max-meetings' => 'You have exceeded the number of open meeting invites'
);
}