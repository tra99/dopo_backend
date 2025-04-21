export interface IUser {
  id: number;
  name: string;
  email: string;
  email_verified_at: null;
  created_at: string;
  updated_at: string;
  school_id: number;
  status: boolean;
  lastest_login: string;
  avatar: null;
  description: null;
  roles: Role[];
}

interface Role {
  id: number;
  name: string;
}
