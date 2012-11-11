package com.kuxhausen.projectmeet;

import java.util.Calendar;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.TimePicker;

public class NewMeetingActivity extends Activity implements OnClickListener {

	Button addPeople, setDuration, setTimeStart, setTimeEnd, setDayStart, setDayEnd;
	RadioGroup contactMethod;

	int duration = 60;
	
	// date and time
	private int mYear;
	private int mMonth;
	private int mStartDay;
	private int mStopDay;
	private int mStartHour;
	private int mStopHour;
	private int mStartMinute;
	private int mStopMinute;

	static final int DURATION_DIALOG_ID = 0;
	static final int START_TIME_DIALOG_ID = 1;
	static final int STOP_TIME_DIALOG_ID = 2;
	static final int START_DATE_DIALOG_ID = 3;
	static final int STOP_DATE_DIALOG_ID = 4;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_newmeeting);

		setDuration = (Button) findViewById(R.id.durationButton);
		setDuration.setOnClickListener(this);
		
		setTimeStart = (Button) findViewById(R.id.startTimeButton);
		setTimeStart.setOnClickListener(this);
		
		setTimeEnd = (Button) findViewById(R.id.stopTimeButton);
		setTimeEnd.setOnClickListener(this);
		
		setDayStart = (Button) findViewById(R.id.startDayButton);
		setDayStart.setOnClickListener(this);
		
		setDayEnd = (Button) findViewById(R.id.stopDayButton);
		setDayEnd.setOnClickListener(this);
		
		addPeople = (Button) findViewById(R.id.addPeopleButton);
		addPeople.setOnClickListener(this);

		final Calendar c = Calendar.getInstance();
		mYear = c.get(Calendar.YEAR);
		mMonth = c.get(Calendar.MONTH);
		mStartDay = c.get(Calendar.DAY_OF_MONTH);
		mStartHour = c.get(Calendar.HOUR_OF_DAY);
		mStartMinute = c.get(Calendar.MINUTE);
		
		mStopHour = mStartHour + duration/60;
		mStopMinute= mStartMinute + duration%60;
		mStopDay = mStartDay;

		updateDisplay();
	}

	@Override
	protected Dialog onCreateDialog(int id) {
		switch (id) {
		case DURATION_DIALOG_ID:
			return new TimePickerDialog(this, mDurationSetListener, duration/60, duration%60,
					true);
		case START_TIME_DIALOG_ID:
			return new TimePickerDialog(this, mStartTimeSetListener, mStartHour, mStartMinute,
					false);
		case STOP_TIME_DIALOG_ID:
			return new TimePickerDialog(this, mStopTimeSetListener, mStopHour, mStopMinute,
					false);
		case START_DATE_DIALOG_ID:
			return new DatePickerDialog(this, mStartDateSetListener, mYear, mMonth,
					mStartDay);
		case STOP_DATE_DIALOG_ID:
			return new DatePickerDialog(this, mStopDateSetListener, mYear, mMonth,
				mStopDay);
		}
		return null;
	}

	@Override
	protected void onPrepareDialog(int id, Dialog dialog, Bundle args) {
		switch (id) {
		case DURATION_DIALOG_ID:
			((TimePickerDialog) dialog).updateTime(duration/60, duration%60);
			break;
		case START_TIME_DIALOG_ID:
			((TimePickerDialog) dialog).updateTime(mStartHour, mStopMinute);
			break;
		case STOP_TIME_DIALOG_ID:
			((TimePickerDialog) dialog).updateTime(mStopHour, mStopMinute);
			break;	
		case START_DATE_DIALOG_ID:
			((DatePickerDialog) dialog).updateDate(mYear, mMonth, mStartDay);
			break;
		case STOP_DATE_DIALOG_ID:
			((DatePickerDialog) dialog).updateDate(mYear, mMonth, mStopDay);
			break;
		}
	}

	private void updateDisplay() {
		setDuration.setText("Event length : " + (duration/60)+":"+pad(duration%60));
		setTimeStart.setText(""+pad(mStartHour)+":"+pad(mStartMinute));
		setTimeEnd.setText(""+pad(mStopHour)+":"+pad(mStopMinute));
		setDayStart.setText(new StringBuilder()
		// Month is 0 based so add 1
		.append(mMonth + 1).append("-").append(mStartDay).append("-")
		.append(mYear).append(" "));
		setDayEnd.setText(new StringBuilder()
		// Month is 0 based so add 1
		.append(mMonth + 1).append("-").append(mStopDay).append("-")
		.append(mYear).append(" "));
				
	}

	private DatePickerDialog.OnDateSetListener mStartDateSetListener = new DatePickerDialog.OnDateSetListener() {
		
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			mYear = year;
			mMonth = monthOfYear;
			mStartDay = dayOfMonth;
			updateDisplay();
		}
	};
private DatePickerDialog.OnDateSetListener mStopDateSetListener = new DatePickerDialog.OnDateSetListener() {
		
		public void onDateSet(DatePicker view, int year, int monthOfYear,
				int dayOfMonth) {
			mYear = year;
			mMonth = monthOfYear;
			mStopDay = dayOfMonth;
			updateDisplay();
		}
	};

	private TimePickerDialog.OnTimeSetListener mDurationSetListener = new TimePickerDialog.OnTimeSetListener() {

		public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
			duration = hourOfDay*60 + minute;
			updateDisplay();
		}
	};
	private TimePickerDialog.OnTimeSetListener mStartTimeSetListener = new TimePickerDialog.OnTimeSetListener() {

		public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
			mStartHour = hourOfDay;
			mStartMinute = minute;
			updateDisplay();
		}
	};
	private TimePickerDialog.OnTimeSetListener mStopTimeSetListener = new TimePickerDialog.OnTimeSetListener() {

		public void onTimeSet(TimePicker view, int hourOfDay, int minute) {
			mStopHour = hourOfDay;
			mStopMinute = minute;
			updateDisplay();
		}
	};

	private static String pad(int c) {
		if (c >= 10)
			return String.valueOf(c);
		else
			return "0" + String.valueOf(c);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.addPeopleButton:
			startActivity(new Intent(this, InvitePeopleActivity.class));
			break;
		case R.id.durationButton:
			showDialog(DURATION_DIALOG_ID, null);
			break;
		case R.id.startTimeButton:
			showDialog(START_TIME_DIALOG_ID, null);
			break;
		case R.id.stopTimeButton:
			showDialog(STOP_TIME_DIALOG_ID, null);
			break;
		case R.id.startDayButton:
			showDialog(START_DATE_DIALOG_ID, null);
			break;
		case R.id.stopDayButton:
			showDialog(STOP_DATE_DIALOG_ID, null);
			break;
		}
	}
}
