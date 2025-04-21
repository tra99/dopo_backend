export interface IParticipant {
  id: number;
  title: null;
  name: string;
  position: null;
  organization: string;
  phone: string;
  email: string;
  address: null;
  avatar: null;
  telegram: null;
  user_id: null;
  created_at: string;
  updated_at: string;
  missions: IMission[];
  user: null;
}

export interface IMission {
  id: number;
  start_date: string;
  end_date: string;
  purpose: string;
  description: string;
  status: string;
  created_at: string;
  updated_at: string;
  pivot: Pivot;
}

interface Pivot {
  participant_id: number;
  mission_id: number;
}
