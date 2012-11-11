package com.kuxhausen.projectmeet;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.database.CharArrayBuffer;
import android.database.Cursor;
import android.database.MergeCursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.BaseColumns;
import android.provider.ContactsContract;
import android.provider.ContactsContract.Contacts;
import android.util.Log;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.ListView;
import android.widget.QuickContactBadge;
import android.widget.ResourceCursorAdapter;
import android.widget.TextView;
import android.widget.Toast;

public class InvitePeopleActivity extends Activity implements OnClickListener {

	Button addPerson;
	ListView addedPeople;
	ContactListItemAdapter adapter;
	Cursor[] cray;
	MergeCursor mergeCursor;

	Toast mToast;
	ResultDisplayer mPendingResult;

	class ResultDisplayer implements OnClickListener {
		String mMsg;
		String mMimeType;

		ResultDisplayer(String msg, String mimeType) {
			mMsg = msg;
			mMimeType = mimeType;
		}

		public void onClick(View v) {
			Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
			intent.setType(mMimeType);
			mPendingResult = this;
			startActivityForResult(intent, 1);
		}
	}

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.activity_invitepeople);

		addedPeople = ((ListView) findViewById(R.id.addedPeopleListView));
		String select = "((" + Contacts.DISPLAY_NAME + " NOTNULL) AND ("
				+ Contacts.HAS_PHONE_NUMBER + "=1) AND ("
				+ Contacts.DISPLAY_NAME + " != '' ))";
		Cursor c = getContentResolver().query(Contacts.CONTENT_URI,
				CONTACTS_SUMMARY_PROJECTION, select, null,
				Contacts.DISPLAY_NAME + " COLLATE LOCALIZED ASC");
		
		cray =new Cursor[1];
		cray[0]=c;
		
		mergeCursor = new MergeCursor(cray);
		
		
		startManagingCursor(c);
		//ContactListItemAdapter adapter = new ContactListItemAdapter(this,
		//		R.layout.quick_contacts, c);
		startManagingCursor(mergeCursor);
		adapter = new ContactListItemAdapter(this,
				R.layout.quick_contacts, mergeCursor);
		addedPeople.setAdapter(adapter);
		

		// Watch for button clicks.
		Button addPerson = ((Button) findViewById(R.id.addPersonButton));
		addPerson.setOnClickListener(new ResultDisplayer("Selected contact",
				ContactsContract.Contacts.CONTENT_ITEM_TYPE));
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {

		}
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		
		if (data != null) {
			Uri uri = data.getData();
			if (uri != null) {
				Cursor c = null;
				try {
					//c = getContentResolver().query(uri,
					//		new String[] { BaseColumns._ID }, null, null, null);
					c = getContentResolver().query(uri,
							CONTACTS_SUMMARY_PROJECTION, null, null, null);
					
					
					mergeCursor.requery();
					Cursor[] crazy = new Cursor[1];//2];
					crazy[0]= mergeCursor;
					//crazy[1] = c;
					mergeCursor = new MergeCursor(crazy);
					startManagingCursor(mergeCursor);
					adapter.changeCursor(mergeCursor);
					
					
					if(c==null)
						Log.e("fdsa","...");
					if(cray[0]==null)
						Log.e("wtf", "");
					Log.e("asdf",data.getDataString());
					
					
					
					/*if (c != null && c.moveToFirst()) {
						int id = c.getInt(0);
						if (mToast != null) {
							mToast.cancel();
						}
						String txt = mPendingResult.mMsg + ":\n" + uri
								+ "\nid: " + id;
						mToast = Toast.makeText(this, txt, Toast.LENGTH_LONG);
						mToast.show();
					}*/
				} finally {
					if (c != null) {
						c.close();
					}
				}
			}
		}
	}

	static final String[] CONTACTS_SUMMARY_PROJECTION = new String[] {
			Contacts._ID, // 0
			Contacts.DISPLAY_NAME, // 1
			Contacts.STARRED, // 2
			Contacts.TIMES_CONTACTED, // 3
			Contacts.CONTACT_PRESENCE, // 4
			Contacts.PHOTO_ID, // 5
			Contacts.LOOKUP_KEY, // 6
			Contacts.HAS_PHONE_NUMBER, // 7
	};

	static final int SUMMARY_ID_COLUMN_INDEX = 0;
	static final int SUMMARY_NAME_COLUMN_INDEX = 1;
	static final int SUMMARY_STARRED_COLUMN_INDEX = 2;
	static final int SUMMARY_TIMES_CONTACTED_COLUMN_INDEX = 3;
	static final int SUMMARY_PRESENCE_STATUS_COLUMN_INDEX = 4;
	static final int SUMMARY_PHOTO_ID_COLUMN_INDEX = 5;
	static final int SUMMARY_LOOKUP_KEY = 6;
	static final int SUMMARY_HAS_PHONE_COLUMN_INDEX = 7;

	private final class ContactListItemAdapter extends ResourceCursorAdapter {
		public ContactListItemAdapter(Context context, int layout, Cursor c) {
			super(context, layout, c);
		}

		@Override
		public void bindView(View view, Context context, Cursor cursor) {
			final ContactListItemCache cache = (ContactListItemCache) view
					.getTag();
			TextView nameView = cache.nameView;
			QuickContactBadge photoView = cache.photoView;
			// Set the name
			cursor.copyStringToBuffer(SUMMARY_NAME_COLUMN_INDEX,
					cache.nameBuffer);
			int size = cache.nameBuffer.sizeCopied;
			cache.nameView.setText(cache.nameBuffer.data, 0, size);
			final long contactId = cursor.getLong(SUMMARY_ID_COLUMN_INDEX);
			final String lookupKey = cursor.getString(SUMMARY_LOOKUP_KEY);
			cache.photoView.assignContactUri(Contacts.getLookupUri(contactId,
					lookupKey));
		}

		@Override
		public View newView(Context context, Cursor cursor, ViewGroup parent) {
			View view = super.newView(context, cursor, parent);
			ContactListItemCache cache = new ContactListItemCache();
			cache.nameView = (TextView) view.findViewById(R.id.name);
			cache.photoView = (QuickContactBadge) view.findViewById(R.id.badge);
			view.setTag(cache);

			return view;
		}
	}

	final static class ContactListItemCache {
		public TextView nameView;
		public QuickContactBadge photoView;
		public CharArrayBuffer nameBuffer = new CharArrayBuffer(128);
	}
}
