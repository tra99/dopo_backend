export interface ICategory {
  id: number;
  title: string;
  parent_id: number;
  type: string;
  status: string;
  created_at: string;
  updated_at: string;
  school_type_id: number;
  questions: IQuestion[];
}

export interface IQuestion {
  id: number;
  category_id: number;
  question: string;
  description: string;
  question_type: string;
  answer_option?: string | null
  rule?: string | null
  created_at: string;
  updated_at: string;
  published_at: null;
  school_type: string;
}
