package com.kuxhausen.projectmeet;

import java.util.ArrayList;

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
import android.provider.ContactsContract.CommonDataKinds.Phone;
import android.provider.ContactsContract.Contacts;
import android.provider.ContactsContract.Data;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.QuickContactBadge;
import android.widget.ResourceCursorAdapter;
import android.widget.TextView;
import android.widget.Toast;

public class InvitePeopleActivity extends Activity implements OnClickListener {

	Button addPerson;
	ListView addedPeople;
	//ContactListItemAdapter adapter;
	CustomAdapter adapter;
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

		// Watch for button clicks.
		Button addPerson = ((Button) findViewById(R.id.addPersonButton));
		addPerson.setOnClickListener(new ResultDisplayer("Selected contact",
				ContactsContract.Contacts.CONTENT_ITEM_TYPE));
		
		ArrayList<ContactListItem> aList = new ArrayList<ContactListItem>();
		ContactListItem a = new ContactListItem();
		a.name = "eric";
		a.contactInfo = "832.552.7666";
		aList.add(a);
		adapter = new CustomAdapter(this,
				R.layout.quick_contacts, aList);
		addedPeople = ((ListView) findViewById(R.id.addedPeopleListView));
		addedPeople.setAdapter(adapter);
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
					Log.e("fdsa",""+c.getCount());
					
					if (c.moveToFirst()) // data?
						   System.out.println(c.getInt(SUMMARY_ID_COLUMN_INDEX)); 
					int myId = (int)c.getInt(SUMMARY_ID_COLUMN_INDEX);
					
					
					/*c = getContentResolver().query(Data.CONTENT_URI,
					          new String[] {Data._ID, Phone.NUMBER, Phone.TYPE, Phone.LABEL},
					          Data.CONTACT_ID + "=?" + " AND "
					                  + Data.MIMETYPE + "='" + Phone.CONTENT_ITEM_TYPE + "'",
					          new String[] {String.valueOf(myId)}, null);
					*/
					
					ContactListItem a = new ContactListItem();
					/*if(c.getColumnIndex(ContactsContract.Contacts.HAS_PHONE_NUMBER)>0)
					{
						
						Cursor pCur = getContentResolver().query(
						         ContactsContract.CommonDataKinds.Phone.CONTENT_URI, 
						         null, 
						         ContactsContract.CommonDataKinds.Phone.CONTACT_ID +" = ?", 
						         new String[]{c.getString(SUMMARY_ID_COLUMN_INDEX)}, null);
						
						
						
					}
					*/
					a.name = ""+c.getString(SUMMARY_NAME_COLUMN_INDEX);
					a.contactInfo = "";
					adapter.add(a);
					
					
					//mergeCursor.requery();
				//	Cursor[] crazy = new Cursor[1];//2];
				//	crazy[0]= mergeCursor;
					//crazy[1] = c;
				//	mergeCursor = new MergeCursor(crazy);
				//	startManagingCursor(mergeCursor);
				//	adapter.changeCursor(mergeCursor);
					
				/*
					
					
					addedPeople = ((ListView) findViewById(R.id.addedPeopleListView));
					String select = "((" + Contacts.DISPLAY_NAME + " NOTNULL) AND ("
							+ Contacts.HAS_PHONE_NUMBER + "=0) AND ("
							+ Contacts.DISPLAY_NAME + " != '' ))";
					 c = getContentResolver().query(Contacts.CONTENT_URI,
							CONTACTS_SUMMARY_PROJECTION, select, null,
							Contacts.DISPLAY_NAME + " COLLATE LOCALIZED ASC");
					
					cray =new Cursor[1];
					cray[0]=c;
					
					Log.e("fdsa",""+c.getCount());
					
					MergeCursor localmergeCursor = new MergeCursor(cray);
					adapter.getCursor().moveToFirst();
					//adapter.changeCursor(c);
					adapter.notifyDataSetChanged();
					adapter.changeCursor(mergeCursor);*/
					
				//	startManagingCursor(c);
					//ContactListItemAdapter adapter = new ContactListItemAdapter(this,
					//		R.layout.quick_contacts, c);
				//	startManagingCursor(mergeCursor);
		//			adapter = new ContactListItemAdapter(this,
		//					R.layout.quick_contacts, mergeCursor);
		//			addedPeople.setAdapter(adapter);
					
					
					
					
				
					
					
					
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
			Contacts.PHOTO_ID, //2
			Contacts.HAS_PHONE_NUMBER // 3
	};

	static final int SUMMARY_ID_COLUMN_INDEX = 0;
	static final int SUMMARY_NAME_COLUMN_INDEX = 1;
	static final int SUMMARY_PHOTO_ID_COLUMN_INDEX = 2;
	static final int SUMMARY_HAS_PHONE_COLUMN_INDEX = 3;

	
		public class CustomAdapter extends ArrayAdapter<ContactListItem>{
		    private ArrayList<ContactListItem> entries;
		    private Activity activity;
		 
		    public CustomAdapter(Activity a, int textViewResourceId, ArrayList<ContactListItem> entries) {
		        super(a, textViewResourceId, entries);
		        this.entries = entries;
		        this.activity = a;
		    }
		 
		    public class ViewHolder{
		        public TextView item1;
		        public TextView item2;
		    }
		 
		    @Override
		    public View getView(int position, View convertView, ViewGroup parent) {
		        View v = convertView;
		        ViewHolder holder;
		        if (v == null) {
		            LayoutInflater vi =
		                (LayoutInflater)activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		            v = vi.inflate(R.layout.grid_item, null);
		            holder = new ViewHolder();
		            holder.item1 = (TextView) v.findViewById(R.id.big);
		            holder.item2 = (TextView) v.findViewById(R.id.small);
		            v.setTag(holder);
		        }
		        else
		            holder=(ViewHolder)v.getTag();
		 
		        final ContactListItem custom = entries.get(position);
		        if (custom != null) {
		            holder.item1.setText(custom.name);
		            holder.item2.setText(custom.contactInfo);
		        }
		        return v;
		    }
		}
	final static class ContactListItem {
		public TextView nameView;
		public String name;
		public String contactInfo;
		//public QuickContactBadge photoView;
		public CharArrayBuffer nameBuffer = new CharArrayBuffer(128);
	}
}