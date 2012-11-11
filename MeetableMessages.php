<?php

/*
	Available variables
	{CREATOR_NAME}
	{USER_NAME}
	{MEETING_NAME}
	{MEETING_RANGE}
	{MEETING_LENGTH}
*/
class MeetableMessages
{
static $smsMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm-time' => 'You have selected your availability {USER_RANGE} for {MEETING_NAME}. To change this please reply with another time or time range.',
'bad-time' => 'The time you sent is not within the available time range {MEETING_RANGE}. Please try again.',
'bad-input' => 'Sorry, we did not understand your response. Please try again.'
);

static $emailMessages = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you. What time or time range can you meet for {MEETING_LENGTH} {MEETING_RANGE}? (HELP for more info)',
'confirm-time' => 'You have selected your availability {USER_RANGE} for {MEETING_NAME}. To change this please reply with another time or time range.',
'bad-time' => 'The time you sent is not within the available time range {MEETING_RANGE}. Please try again.',
'bad-input' => 'Sorry, we did not understand your response. Please try again.'
);

static $emailSubjects = array(
'meeting-kick-off' => '{CREATOR_NAME} wants to schedule {MEETING_NAME} with you',
'confirm-time' => 'Confirmation of your time for {MEETING_NAME}',
'bad-time' => 'The time you send is invalid',
'bad-input' => 'We had trouble understanding your reply.'
);
}